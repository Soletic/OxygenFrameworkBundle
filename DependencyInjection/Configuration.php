<?php

namespace Oxygen\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration extends OxygenConfiguration
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oxygen_framework');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
        	->children()
	        	->arrayNode('templating')
	        		->addDefaultsIfNotSet()
	        		->children()
	        			->variableNode('layouts')->defaultValue(array('full' => '::base.html.twig'))->end()
	        		->end()
	        	->end()
	        ->end();

        return $treeBuilder;
    }
}
