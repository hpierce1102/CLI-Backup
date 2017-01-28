<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\UserBuilder;

use Backup\User\AmazonS3User;
use Backup\UserBuilder\AmazonS3UserBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class AmazonS3UserBuilderTest extends TestCase
{
    public function testCreatesUser()
    {
        $builder = new AmazonS3UserBuilder();

        $output = new NullOutput();
        $actualUser = $builder->buildUser($output);

        $expectedUser = new AmazonS3User();
        $expectedUser->setBucket('Placeholder');
        $expectedUser->setProfile('Placeholder');
        $expectedUser->setRegion('Placeholder');

        $this->assertEquals($expectedUser, $actualUser);
    }
}