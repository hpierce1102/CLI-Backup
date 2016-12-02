<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\Command;

use Backup\StorageEngine\StorageEngineInterface;
use Backup\Util\ClassFinder;
use Backup\Util\ConfigParser;
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

            ->addOption('json', null, InputOption::VALUE_NONE , 'Run with this option to get json output only.');
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = ConfigParser::getConfig();

        $users = new \stdClass();
        foreach(ClassFinder::getClassesInNamespace('Backup\\StorageEngine', 'Backup\\StorageEngine\\StorageEngineInterface') as $class){

            /** @var StorageEngineInterface $storageEngine */
            $storageEngine = $class::initFromConfig($config);

            $users->$class = $storageEngine->listUsers();
        }

        if($input->getOption('json')){
            $json = json_encode($users);
            $output->write($json);
        } else {
            $storageEngines = (array) $users;

            foreach($storageEngines as $key => $storageEngineUsers){
                $output->writeln(sprintf('Showing users stored in: %s' ,$key));

                if(empty($storageEngineUsers)){
                    $output->writeln('  NONE');
                    continue;
                }

                foreach($storageEngineUsers as $user){
                    $output->writeln('  ' . $user['alias']);
                    $userParams = (array) $user['user'];

                    foreach($userParams as $paramKey => $paramValue){
                        $output->writeln(sprintf("    %s : %s", $paramKey, $paramValue));
                    }
                }
            }
        }


    }
}