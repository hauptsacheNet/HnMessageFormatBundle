<?php

namespace Hn\MessageFormatBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('hn_message_format')
            ->children()
                ->scalarNode('app_name')->end()
                ->arrayNode('redis')
                    ->children()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->defaultValue('6379')->end()
                        ->scalarNode('list')->defaultValue('logstash')->end()
                        ->scalarNode('password')->end()
                    ->end()
                ->end()
                ->arrayNode('handler')
                    ->children()
                        ->scalarNode('level')->defaultValue('debug')->end()
                        ->scalarNode('bubble')->defaultValue('true')->end()
                    ->end()
                ->end()
            ->end()
        ;


        return $treeBuilder;
    }
}
