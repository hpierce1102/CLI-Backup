<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\Command;

use Backup\Util\ClassFinder;
use Backup\Util\ConfigParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterUserCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('registerUser')
            ->setDescription('adds a new user and their settings.')
            ->setHelp("")

            ->addOption("file", "f", InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = ConfigParser::getConfig();
        
        foreach(ClassFinder::getClassesInNamespace('Backup\\StorageEngine') as $storageEngine){
            var_dump($storageEngine);
        }
    }
}