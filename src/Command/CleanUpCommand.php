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

class CleanUpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cleanUp')
            ->setDescription('Removes old backups from a given user')
            ->setHelp("")

            ->addArgument('userAlias', InputArgument::OPTIONAL, "Which user configuration should be used?")
            ->addArgument('age', InputArgument::OPTIONAL, "Items in the backup older than this will be deleted.", "2 weeks");
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storageEngine = ConfigParser::getStorageEngine();

        $userAlias = $input->getArgument('userAlias');

        /** @var UserInterface $user */
        $user = $storageEngine->retrieveUser($userAlias);


        $uploaderClass = $user->getUploaderClass();

        /** @var UploaderInterface $uploader */
        $uploader = new $uploaderClass($user);
        $uploader->setOutput($output);

        $files = $uploader->listFiles('/');

        $maxAge = \DateInterval::createFromDateString($input->getArgument('age'));

        $maxAgeSeconds = $this->convertDateIntervalToSeconds($maxAge);
        $now = new \DateTime('now');

        $files = array_filter($files, function($file) use ($now, $maxAgeSeconds){
            /** @var \DateTime $date */
            $date = $file['date'];
            $seconds = $now->getTimestamp() - $date->getTimestamp();
            return $seconds > $maxAgeSeconds;
        });

        $files = array_map(function($file){
            return $file['location'];
        }, $files);

        if(!empty($files)){
            $fileCount = count($files);
            $output->writeln(sprintf('Deleting %s files.', $fileCount));
            $uploader->purgeFiles($files);
            $output->writeln(sprintf('Deleted %s files successfully.', $fileCount));
        } else {
            $output->writeln('No files were old enough to delete.');
        }
    }

    private function convertDateIntervalToSeconds(\DateInterval$maxAge){
        $seconds = $maxAge->s;
        $seconds += ($maxAge->i * 60);
        $seconds += ($maxAge->h * 3600);
        $seconds += ($maxAge->d * 86400);
        //This is calculated conservatively as 31 days because "1 month" is acceptable input from PHP's standpoint
        //but is quite ambiguous when trying to convert it to seconds as some months have 31 days and February
        //has only 28 days. We use the 31 days because that will result in less purged files, which is less damaging
        //than accidentally deleting files that *weren't* supposed to be deleted.
        $seconds += ($maxAge->m * 2678400);
        $seconds += ($maxAge->y * 31536000);

        return $seconds;
    }
}