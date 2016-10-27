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

class RestoreCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('restore')
            ->setDescription('Downloads the files from the remote source and saves them to disk.')
            ->setHelp("")

            ->addArgument('userAlias', InputArgument::OPTIONAL, "Which user configuration should be used?")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}