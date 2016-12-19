<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Uploader;

use Aws\S3\S3Client;
use Backup\User\AmazonS3User;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class AmazonS3Uploader implements UploaderInterface
{
    /** @var AmazonS3User  */
    protected $user;

    /** @var S3Client */
    protected $s3;

    /** @var  OutputInterface | null */
    protected $output;

    public function __construct(AmazonS3User $user)
    {
        $this->user = $user;

        $this->s3 = new S3Client([
            'profile' => $user->getProfile(), //Which credential to use.
            'version' => 'latest',
            'region'  => $user->getRegion()
        ]);
    }

    public function publishFiles(Array $assocArray)
    {
        if(isset($this->output)){
            $progressBar = new ProgressBar($this->output, count($assocArray));
        }

        foreach($assocArray as $filePath => $location){
            $this->publishFile($filePath, $location);

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
        $location = trim($location, '/');

        $result = $this->s3->putObject(array(
            'Bucket'     => $this->user->getBucket(),
            'Key'        => $location,
            'SourceFile' => $filePath,
        ));

        return $result;
        
    }

    public function purgeFile(String $location)
    {
        $location = trim($location, '/');

        $this->s3->deleteObject(array(
            'Bucket' => $this->user->getBucket(),
            'Key' => $location
        ));
    }

    public function purgeFiles(Array $assocArray)
    {
        foreach($assocArray as $location){
            $this->purgeFile($location);
        }
    }

    public function listFiles(String $location)
    {
        $location = ltrim($location, '/');

        $options = array(
            'Bucket' => $this->user->getBucket()
        );

        if(!empty($location)){
            $options['Prefix'] = $location;
        }

        $objects = $this->s3->getIterator('ListObjects', $options);

        $files = [];
        var_dump($objects);
        foreach($objects as $object){
            $objectArray = array(
                'location' => $object['Key'],
                'date' => $object['LastModified'] //This object extends DateTime.
            );
            $files[] = $objectArray;
        }

        return $files;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}