<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('Config');

        $rootNode
            ->children()
                ->scalarNode('DefaultStorageEngine')
                ->end()
                ->arrayNode('StorageEngine')
                    ->children()
                        ->arrayNode('SQLite')
                            ->children()
                                ->scalarNode('file')->end()
                            ->end()
                        ->end()
                        ->arrayNode('JSON')
                            ->children()
                                ->scalarNode('file')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}