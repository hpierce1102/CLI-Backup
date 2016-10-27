<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\Command;

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

    }
}