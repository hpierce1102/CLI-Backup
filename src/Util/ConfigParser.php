<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Util;

use Backup\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class ConfigParser
{
    const appRoot = __DIR__ . '/../../';

    public static function getConfig()
    {
        $config = Yaml::parse(
            file_get_contents(self::appRoot . 'config/config.yml')
        );

        $processor = new Processor();
        $configuration = new Configuration();
        $configs = [ $config ];
        
        return $processor->processConfiguration(
            $configuration,
            $configs
        );
    }
}