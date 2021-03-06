<?php
/**
 * Finds all of the classes in a defined namespace based on the mappings defined in composer.json
 *
 * More info: http://stackoverflow.com/a/40229665/3000068
 *
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\Util;

class ClassFinder
{
    //This value should be the directory that contains composer.json
    const appRoot = __DIR__ . "/../../";

    public static function getClassesInNamespace(String $namespace, String $interface=null)
    {
        $directory = self::getNamespaceDirectory($namespace);

        if(empty($directory)){
            throw new \InvalidArgumentException(sprintf(
                '%s did not map to a physical directory. Check that it exists, follows PSR-4 guidelines, and is listed in composer.json.',
                $namespace));
        }

        $files = scandir($directory);

        $classes = array_map(function($file) use ($namespace){
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        $classes = array_filter($classes, function($possibleClass){
            return class_exists($possibleClass);
        });

        if(isset($interface)){
            $classes = array_filter($classes, function($possibleClass) use ($interface){
                $reflection = new \ReflectionClass($possibleClass);
                return in_array($interface, $reflection->getInterfaceNames());
            });
        }

        return $classes;
    }

    private static function getDefinedNamespaces()
    {
        $composerJsonPath = self::appRoot . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array) $composerConfig->autoload->$psr4;
    }

    private static function getNamespaceDirectory($namespace)
    {
        $composerNamespaces = self::getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if(array_key_exists($possibleNamespace, $composerNamespaces)){
                return realpath(self::appRoot . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments));
            }

            $undefinedNamespaceFragments[] = array_pop($namespaceFragments);
        }

        return false;
    }
}