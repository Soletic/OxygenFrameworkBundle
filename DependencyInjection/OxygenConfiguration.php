<?php

namespace Oxygen\FrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
abstract class OxygenConfiguration
{
	/**
	 * 
	 * @var ArrayNodeDefinition
	 */
	private $entitiesNode = null;
	
	private function camelCaseStringToUnderScores($str) {
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
	
	/**
	 * Add the node entities
	 * 
	 * @param ArrayNodeDefinition|NodeDefinition $rootNode
	 */
	private function initNodeEntities($rootNode) {
		$this->entitiesNode = $rootNode->children()->arrayNode('entities')->addDefaultsIfNotSet();
	}
	/**
	 * 
	 * @param ArrayNodeDefinition|NodeDefinition $rootNode
	 * @param string $entity_class The full path of the entity class
	 * @param string $repository_class The full path of the repository class
	 */
    public function addEntityConfiguration($rootNode, $entity_class, $repository_class)
    {
    	if (is_null($this->entitiesNode))
    		$this->initNodeEntities($rootNode);
    	$entity_name = explode("\\", $entity_class);
    	$entity_name = $this->camelCaseStringToUnderScores(array_pop($entity_name));
		
    	$this->entitiesNode
    		->children()
    			->arrayNode($entity_name)
    				->addDefaultsIfNotSet()
    				->children()
			        	->scalarNode('class')->defaultValue($entity_class)->end()
			        	->scalarNode('repository')->defaultValue($repository_class)->end()
			        ->end()
			    ->end()
	        ->end();
    }
}
