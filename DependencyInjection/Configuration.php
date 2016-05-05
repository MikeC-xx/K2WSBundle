<?php

namespace K2\K2WSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('k2_ws');

        $rootNode
            ->children()
                ->scalarNode('host')->isRequired()->end()
                ->scalarNode('name')->isRequired()->end()
                ->scalarNode('port')->treatNullLike(80)->isRequired()->end()
                ->booleanNode('secure')->treatNullLike(false)->isRequired()->end()
                ->scalarNode('username')->isRequired()->end()
                ->scalarNode('password')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
