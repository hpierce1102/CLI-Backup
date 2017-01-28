<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\Util;

use Backup\User\GoogleServiceAccountUser;
use PHPUnit\Framework\TestCase;

class GoogleServiceAccountUserTest extends TestCase
{
    public function testUserNormal()
    {
        $user = new GoogleServiceAccountUser();

        $user->setPathToPrivateKeyFile('../somePath.json');
        $actualValue = $user->getPathToPrivateKeyFile();
        $expectedValue = '../somePath.json';
        $this->assertEquals($expectedValue, $actualValue);

        $user->setAppName('Backup-CLI');
        $actualValue = $user->getAppName();
        $expectedValue = 'Backup-CLI';
        $this->assertEquals($expectedValue, $actualValue);

        $user->setGoogleAppsEmail('example@gmail.com');
        $actualValue = $user->getGoogleAppsEmail();
        $expectedValue = 'example@gmail.com';
        $this->assertEquals($expectedValue, $actualValue);

        $user->setClientId('123456789abcdefghijklmnop');
        $actualValue = $user->getClientId();
        $expectedValue = '123456789abcdefghijklmnop';
        $this->assertEquals($expectedValue, $actualValue);
    }
}
