<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Util;

use Backup\Configuration;
use Backup\Exception\InvalidConfigStateException;
use Backup\StorageEngine\StorageEngineInterface;
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

    public static function getStorageEngine()
    {
        $config = self::getConfig();

        //Get the storageEngine to use based on the config.
        $testedStorageEngineNames = [];
        /** @var StorageEngineInterface $storageEngine */
        foreach(ClassFinder::getClassesInNamespace('Backup\\StorageEngine',
            'Backup\\StorageEngine\\StorageEngineInterface') as $storageEngine){
            if($config['DefaultStorageEngine'] == $storageEngine::getName()) {
                $storageEngine = $storageEngine::initFromConfig($config);
                break;
            } else {
                $testedStorageEngineNames[] = $storageEngine::getName();
            }

            unset($storageEngine);
        }

        if(!isset($storageEngine)){
            throw new InvalidConfigStateException(sprintf(
                'Could not load a storage engine. Provided name: %s. Loaded names: %s',
                $config['DefaultStorageEngine'], implode(', ', $testedStorageEngineNames)));
        } else {
            return $storageEngine;
        }
    }
}