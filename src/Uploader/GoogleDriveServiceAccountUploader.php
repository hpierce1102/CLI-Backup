<?php
/**
 * Publishes files to Google Drive under a service account and shares them with a
 * given user, thus giving read only access to those files.
 *
 * This class is working, but still largely a WIP:
 * TODO: purgeFiles and publish files should consider looking into batch processing.
 * TODO: Add PHPDoc annotations and descriptions to functions.
 * TODO: Storing a cache might be nice, to prevent multiple HTTP requests for information we already found.
 * TODO: Work with data provided from a config file, rather than my non-secret info hardcoded into the class.
 *
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Uploader;

use Backup\User\GoogleServiceAccountUser;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class GoogleDriveServiceAccountUploader implements UploaderInterface
{
    private $folderIds;
    private $filesShared;
    private $rateLimitCounter = 0;

    protected $user;
    protected $service;
    protected $output;
    
    const SCOPES = \Google_Service_Drive::DRIVE . ' ' ;

    public function __construct(GoogleServiceAccountUser $user)
    {
        $this->folderIds = [];
        $this->filesShared = [];
        $this->user = $user;

        $client = new \Google_Client();
        $client->setApplicationName($user->getAppName());
        $client->setAuthConfigFile($user->getPathToPrivateKeyFile());
        $client->setClientId($user->getClientId());
        $client->setAccessType('offline_access');
        $client->setScopes(self::SCOPES);

        $this->service = new \Google_Service_Drive($client);
    }

    public function publishFiles(Array $files)
    {
        if(isset($this->output)){
            $progressBar = new ProgressBar($this->output, count($files));
        }

        foreach($files as $key => $value){
            $this->publishFile($key, $value);

            if(isset($progressBar)){
                $progressBar->advance();
            }
        }

        if(isset($progressBar)){
            $progressBar->finish();
        }
    }

    public function publishFile(String $filePath, String $location)
    {
        $location = ltrim($location, '/');
        
        if(!file_exists($filePath)){
            throw new \InvalidArgumentException(sprintf('Could not find file at provided filepath: %s', $filePath));
        }

        $fileContent = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath);

        $folderId = $this->getFolderForFile($location);
        $name = $this->getFileName($location);

        $fileMetaData = array(
            'name' => $name,
        );

        if(null !== $folderId){
            $fileMetaData['parents'] = array($folderId);
        }

        $this->rateLimit();
        $this->service->files->create(
            new \Google_Service_Drive_DriveFile($fileMetaData),
            array(
                'data' => $fileContent,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart'
            )
        );
        
        $rootFolder = $this->getRootFolder($location);
        $this->shareWithUser($rootFolder);

        return true;
    }

    public function purgeFile(String $location)
    {
        $fileId = $this->findFileId($location);
        $this->rateLimit();
        $this->service->files->delete($fileId);
    }
    
    public function purgeFiles(Array $files)
    {
        foreach($files as $file){
            $this->purgeFile($file);
        }
    }
    
    public function listFiles(String $filePath)
    {
        $files = [];
        /** @var \Google_Service_Drive_DriveFile $file */
        foreach($this->listFilesGenerator() as $file){
            $metadata = [];
            $metadata['location'] = $this->resolveLocation($file->getId());
            $metadata['date'] = $file->getCreatedTime();

            $files[] = $metadata;
        }

        $filePath = trim($filePath, '/');

        return array_filter($files, function($file) use ($filePath){
            return substr($file['location'], 0, strlen($filePath)) == $filePath;
        });
    }

    private function shareWithUser(String $location)
    {
        //Check if the root folder is shared with user locally.
        if(in_array($location, $this->filesShared)){
            return true;
        }
        //Check if the root folder is shared with user via google drive.
        $this->rateLimit();
        $files = $this->service->files->listFiles(array(
            'q' => "name='$location' and '{$this->user->getGoogleAppsEmail()}' in readers"
        ))->getFiles();


        if(count($files) == 0){
            $shareFileId = $this->findFileId($location);

            $emailPermission = new \Google_Service_Drive_Permission(array(
                'type' => 'user',
                'role' => 'reader',
                'emailAddress' => $this->user->getGoogleAppsEmail()
            ));

            $this->rateLimit();
            $this->service->permissions->create(
                $shareFileId, $emailPermission, array('fields' => 'id'));
        }

        $this->filesShared[] = $location;

        return true;
    }

    private function resolveLocation($fileId)
    {
        /** @var \Google_Service_Drive_DriveFile $file */
        $this->rateLimit();
        $file = $this->service->files->get($fileId, array(
           'fields' => 'name, id, parents'
        ));

        if($parents = $file->getParents()){
            return ltrim($this->resolveLocation($parents[0]) . "/" . $file->getName(), '/');
        } else {
            //The topmost directory is "My Drive" - that's not compatible with our interface
            return '';
        }
    }

    private function listFilesGenerator()
    {
        $pageToken = null;
        do{
            $this->rateLimit();
            $response = $this->service->files->listFiles(array(
                'pageSize' => 1000,
                'pageToken' => $pageToken,
                'fields' => 'files(id, parents)'
            ));
            $pageToken = $response['nextPageToken'];
            $files = $response->getFiles();

            foreach($files as $file){
                yield $file;
            }
        } while($pageToken);
    }

    private function findFileId(String $location)
    {
        $files = $this->findFile($location);
        return $files[0]['id'];
    }

    private function findFile(String $filePath, String $parentId = null)
    {
        $fileFragments = explode("/", $filePath);
        $fileToSearch = $fileFragments[0];

        $query = "name='$fileToSearch'";

        if(count($fileFragments) > 1){
            $query .= " and mimeType = 'application/vnd.google-apps.folder'";
        }

        if(!is_null($parentId)){
            $query .= " and '$parentId' in parents";
        }

        $this->rateLimit();
        $files = $this->service->files->listFiles(array(
            'q' => $query
        ))->getFiles();

        if(count($fileFragments) == 1){
            return $files;
        } else {
            unset($fileFragments[0]);
            return $this->findFile(implode('/', $fileFragments), $files[0]['id']);
        }
    }

    private function getFileName(String $location)
    {
        $fileFragments = explode('/', $location);
        return array_pop($fileFragments);
    }

    private function getRootFolder(String $location)
    {
        $fileFragments = explode('/', $location);

        if(count($fileFragments) >= 2){
            return $fileFragments[0];
        } else {
            return null;
        }
    }

    private function getFolderForFile(String $filePath)
    {
        if($folderId = $this->findFolderIdForFile($filePath)){
            return $folderId;
        } else {
            return $this->createFolderForFile($filePath);
        }
    }

    private function findFolderIdForFile(String $filePath)
    {
        $pathFragments = explode('/', $filePath);
        array_pop($pathFragments);
        return $this->findFolderId(implode('/', $pathFragments));
    }

    private function findFolderId(String $folderToFind)
    {
        if(!isset($this->folderIds[$folderToFind])){
            $folderId = $this->findFolderIdRecursive($folderToFind);
            $this->folderIds[$folderToFind] = $folderId;
        }

        return $this->folderIds[$folderToFind];
    }

    private function findFolderIdRecursive(String $folderToFind, String $insideFolderId = null)
    {
        $pathFragments = explode('/', $folderToFind);

        $searchParams = array(
            'q' => "name='$pathFragments[0]'",
            'mimeType' => 'application/vnd.google-apps.folder',
        );

        if(isset($insideFolderId)){
            $searchParams['q'] .= "and '$insideFolderId' in parents";
        }

        $this->rateLimit();
        $folder = $this->service->files->listFiles($searchParams)->getFiles();

        if(count($folder) == 0){
            return null;
        }
        
        $folderId = $folder[0]['id'];
        
        if(count($pathFragments) >= 2){
            unset($pathFragments[0]);
            return $this->findFolderIdRecursive(implode("/", $pathFragments), $folderId);
        } else {
            return $folderId;
        }
    }

    private function createFolderForFile(String $filePath)
    {
        $pathFragments = explode('/', $filePath);
        array_pop($pathFragments);
        return $this->createFolder(implode('/', $pathFragments));
    }

    private function createFolder(String $folder)
    {
        if(!isset($this->folderIds[$folder]) || $this->folderIds[$folder] === null){
            $folderId = $this->createFolderRecursive($folder);
            $this->folderIds[$folder] = $folderId;
        }

        return $this->folderIds[$folder];
    }

    private function createFolderRecursive(String $folderToCreate)
    {
        $pathFragments = explode('/', $folderToCreate);

        $parentId = null;
        foreach($pathFragments as $pathFragment){
            if($tempParentId = $this->findFolderIdRecursive($pathFragment, $parentId)){
                $parentId = $tempParentId;
            } else {
                $parentId = $this->createSingleFolder($pathFragment, $parentId);
            }
        }

        return $parentId;
    }

    private function createSingleFolder(String $name, String $parentId = null)
    {
        $fileMetadata = array(
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        );

        if(isset($parentId)){
            $fileMetadata['parents'] = array($parentId);
        }

        $this->rateLimit();
        $folder = $this->service->files->create(
            new \Google_Service_Drive_DriveFile($fileMetadata),
            array(
                'uploadType' => 'multipart'
            )
        );

        return $folder['id'];
    }

    /*
     * Originally, rate limiting was planned to be implemented by overriding Google_Client's HTTP client (Guzzle\Client).
     * However, the Google PHP SDK decided to hardcode in 'new Client' everywhere (PHPStorm has 41 hits - some of
     * which are comments) and overriding all 30-odd methods all over the SDK seemed impractical. Additionally,
     * pausing in the HTTP server meant that authentication was rate limited in addition to the queries that we called.
     *
     * Thus using this method within this class was deemed the best method because it prevents us from having to
     * effectively maintain a fork within this project of the Google PHP SDK and gives us fine grain control over what
     * is rate limited and want isn't.
     *
     * Because doing that is far worse than calling a private method before each API access.
     */
    private function rateLimit()
    {
        if($this->rateLimitCounter++ >= 2){
            sleep(1);
            $this->rateLimitCounter = 0;
        }
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}