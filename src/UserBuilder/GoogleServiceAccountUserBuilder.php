<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\UserBuilder;

use Backup\User\GoogleServiceAccountUser;
use Symfony\Component\Console\Output\OutputInterface;

class GoogleServiceAccountUserBuilder implements UserBuilderInterface
{

    public static function getName()
    {
        return "GoogleServiceAccount";
    }

    public function buildUser(OutputInterface $output)
    {
        $appName = readline('AppName:');

        $defaultPath = 'config/GoogleServiceAccountSecret.json';
        $pathToPrivateKeyFile = readline("Path to private key file ($defaultPath):");
        $pathToPrivateKeyFile = empty($pathToPrivateKeyFile) ? $defaultPath : $pathToPrivateKeyFile;

        $clientId = readline("ClientId:");

        $user = new GoogleServiceAccountUser();
        $user->setAppName($appName);
        $user->setPathToPrivateKeyFile($pathToPrivateKeyFile);
        $user->setClientId($clientId);
    }
}