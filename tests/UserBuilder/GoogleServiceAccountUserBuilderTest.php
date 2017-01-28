<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\Util;

use Backup\User\GoogleServiceAccountUser;
use Backup\UserBuilder\GoogleServiceAccountUserBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class GoogleServiceAccountUserBuilderTest extends TestCase
{
    public function testCreatesUser()
    {
        $builder = new GoogleServiceAccountUserBuilder();

        $output = new NullOutput();
        $actualUser = $builder->buildUser($output);

        $expectedUser = new GoogleServiceAccountUser();
        $expectedUser->setAppName('Placeholder');
        $expectedUser->setClientId('Placeholder');
        $expectedUser->setGoogleAppsEmail('Placeholder');
        $expectedUser->setPathToPrivateKeyFile('Placeholder');

        $this->assertEquals($expectedUser, $actualUser);
    }
}
