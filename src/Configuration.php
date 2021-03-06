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
                ->arrayNode('Notifier')
                    ->children()
                        ->integerNode('notificationLevel')
                            ->defaultValue(2)
                            ->info('When should emails be sent? 0 - Never, 1 - On errors, 3 - literally everything.')
                        ->end()
                        ->arrayNode('EmailNotifier')
                            ->children()
                                ->booleanNode('enabled')->end()
                                ->scalarNode('fromAddress')->end()
                                ->arrayNode('toAddresses')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('SMTPHost')->end()
                                ->scalarNode('SMTPPort')->end()
                                ->scalarNode('SMTPEncryption')
                                    ->info('Example: tls, ssl')
                                ->end()
                                ->scalarNode('SMTPUserName')->end()
                                ->scalarNode('SMTPPassword')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
//                ->arrayNode()
                ->scalarNode('DefaultStorageEngine')
                    ->defaultValue('SQLite')
                ->end()
                ->arrayNode('StorageEngine')
                    ->children()
                        ->arrayNode('SQLite')
                            ->children()
                                ->scalarNode('file')
                                    ->defaultValue('config/sqlite.sql')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('JSON')
                            ->children()
                                ->scalarNode('file')
                                    ->defaultValue('config/users.json')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('files')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('sourceFile')->end()
                            ->scalarNode('location')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}