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

    }
}