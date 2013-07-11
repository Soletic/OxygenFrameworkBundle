<?php

namespace Oxygen\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
abstract class OxygenConfiguration
{

    public function addEntityConfiguration($rootNode, $entity_class, $repository_class)
    {
       $rootNode
        	->children()
	        	->arrayNode('entities')
	        		->addDefaultsIfNotSet()
	        		->children()
	        			->arrayNode('pass')
	        				->addDefaultsIfNotSet()
	        				->children()
	        					->scalarNode('class')->defaultValue($entity_class)->end()
	        					->scalarNode('repository')->defaultValue($repository_class)->end()
	        				->end()
	        			->end()
	        		->end()
	        	->end()
	        ->end();
    }
}