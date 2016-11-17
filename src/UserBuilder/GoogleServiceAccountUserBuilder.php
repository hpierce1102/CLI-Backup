<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\UserBuilder;

use Backup\User\GoogleServiceAccountUser;
use Backup\Util\Readline;
use Symfony\Component\Console\Output\OutputInterface;

class GoogleServiceAccountUserBuilder implements UserBuilderInterface
{

    public static function getName()
    {
        return "GoogleServiceAccount";
    }

    public function buildUser(OutputInterface $output)
    {
        $appName = Readline::readline('AppName:');

        $defaultPath = 'config/GoogleServiceAccountSecret.json';
        $pathToPrivateKeyFile = Readline::readline("Path to private key file ($defaultPath):");
        $pathToPrivateKeyFile = empty($pathToPrivateKeyFile) ? $defaultPath : $pathToPrivateKeyFile;

        $clientId = Readline::readline("ClientId:");

        $googleAppsEmail = Readline::readline("Who should the files be shared with? GoogleAppsEmail:");

        $user = new GoogleServiceAccountUser();
        $user->setAppName($appName);
        $user->setPathToPrivateKeyFile($pathToPrivateKeyFile);
        $user->setClientId($clientId);
        $user->setGoogleAppsEmail($googleAppsEmail);

        return $user;
    }
}