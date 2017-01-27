<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Tests\Util;

use Backup\StorageEngine\StorageEngineInterface;
use Backup\Util\ConfigParser;
use PHPUnit\Framework\TestCase;

class ConfigParserTest extends TestCase
{
    public function testConfigParses()
    {
        $config = ConfigParser::getConfig();

        $this->assertTrue(is_array($config));
    }

    public function testStorageEngine()
    {
        $storageEngine = ConfigParser::getStorageEngine();

        $this->assertTrue($storageEngine instanceof StorageEngineInterface);
    }
}
?>