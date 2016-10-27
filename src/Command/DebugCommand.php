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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DebugCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('debug')
            ->setDescription('Displays the state of the application.')
            ->setHelp("Displays the state of the application.")

            //Choices: Users or StorageEngines
            ->addArgument('entity', InputArgument::REQUIRED, "Which entity do you need to check?")
            ->addOption('json', null, InputOption::VALUE_NONE , 'Run with this option to get json output only.');
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}