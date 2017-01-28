<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\User;

use Backup\User\AmazonS3User;
use PHPUnit\Framework\TestCase;

class AmazonS3UserTest extends TestCase
{
    public function testUserNormal()
    {
        $user = new AmazonS3User();

        $user->setRegion('us-west-2');
        $actualValue = $user->getRegion();
        $expectedValue = 'us-west-2';
        $this->assertEquals($expectedValue, $actualValue);

        $user->setProfile('default');
        $actualValue = $user->getProfile();
        $expectedValue = 'default';
        $this->assertEquals($expectedValue, $actualValue);

        $user->setBucket('foo.bar.bucket');
        $actualValue = $user->getBucket();
        $expectedValue = 'foo.bar.bucket';
        $this->assertEquals($expectedValue, $actualValue);
    }
}
