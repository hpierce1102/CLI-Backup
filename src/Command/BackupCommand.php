<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\Command;

use Backup\Uploader\UploaderInterface;
use Backup\User\UserInterface;
use Backup\Util\ConfigParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('backup')
            ->setDescription('Performs the backup by writing to')
            ->setHelp("")
            
            ->addArgument('userAlias', InputArgument::OPTIONAL, "Which user configuration should be used?")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storageEngine = ConfigParser::getStorageEngine();
        $config = ConfigParser::getConfig();

        $userAlias = $input->getArgument('userAlias');

        /** @var UserInterface $user */
        $user = $storageEngine->retrieveUser($userAlias);

        $uploaderClass = $user->getUploaderClass();

        /** @var UploaderInterface $uploader */
        $uploader = new $uploaderClass($user);
        $uploader->setOutput($output);

        foreach($config['files'] as $filePair){
            $files = $this->getFilePaths($filePair['sourceFile'], $filePair['location']);
            $uploader->publishFiles($files);
        }
    }

    protected function getFilePaths($filesystemPath, $location)
    {
        $directory = new \RecursiveDirectoryIterator($filesystemPath);
        $iterator = new \RecursiveIteratorIterator($directory);

        $files = [];
        /** @var \SplFileInfo $splfileinfo */
        foreach($iterator as $splfileinfo){
            $files[] = $splfileinfo->getPathname();
        }

        $files = array_filter($files, function($filePath){
            $fileSegments = explode('/', $filePath);
            $filename = array_pop($fileSegments);
            return $filename !== '.' && $filename !== '..';
        });

        foreach($files as $key => $filePath){
            $chars = strlen($filesystemPath);
            $knownPath = substr($filePath, $chars);
            $files[$filePath] = $location . $knownPath;
            unset($files[$key]);
        }

        return $files;
    }
}