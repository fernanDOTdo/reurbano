<?php

namespace Reurbano\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
* Generates the configuration tree.
*
* @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
*/
class Configuration
{
/**
* Generates the configuration tree.
*
* @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
*/
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('reurbano', 'array');
        $rootNode
        ->children()
            ->scalarNode('default_city')->defaultValue('sao-paulo')->end()
            ->arrayNode('quova')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('apikey')->defaultFalse()->end()
                        ->scalarNode('secret')->defaultFalse()->end()
                    ->end()
                ->end()
        ->end();
        return $treeBuilder->buildTree();
    }

}