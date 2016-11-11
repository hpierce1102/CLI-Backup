<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\Command;

use Backup\StorageEngine\StorageEngineInterface;
use Backup\UserBuilder\UserBuilderInterface;
use Backup\Util\ClassFinder;
use Backup\Util\ConfigParser;
use Backup\Util\Readline;
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
        $storageEngine = ConfigParser::getStorageEngine();

        //Query the user for a user alias
        $userAlias = Readline::readline('Provide a user alias:');

        //Query the user to determine which type of user to create
        $loadedUserBuilderClasses = ClassFinder::getClassesInNamespace('Backup\\UserBuilder', 'Backup\\UserBuilder\\UserBuilderInterface');
        $userBuilderMessages = [];
        /** @var UserBuilderInterface $userBuilder */
        foreach($loadedUserBuilderClasses as $key => $userBuilder){
            $userBuilderMessages[] = sprintf("[%s] - %s", $key, $userBuilder::getName());
        }

        $messages = [
            'Which type of user would you like to create?',
            '============================================'
        ];
        $messages = array_merge($messages, $userBuilderMessages);
        $output->writeln($messages);
        do{
            $input = Readline::readline('Pick a user builder: ');

            if(isset($loadedUserBuilderClasses[$input])){
                /** @var UserBuilderInterface $userBuilder */
                $userBuilder = new $loadedUserBuilderClasses[$input]();
            } else {
                $output->writeln("<error>Invalid input. Please try again.</error>");
            }
        } while(!isset($userBuilder));

        $output->writeln(sprintf('<info>Successfully set User Builder to: %s</info>', $userBuilder::getName()),
            OutputInterface::VERBOSITY_DEBUG);

        $user = $userBuilder->buildUser($output);

        $storageEngine->persistUser($userAlias, $user);
    }
}