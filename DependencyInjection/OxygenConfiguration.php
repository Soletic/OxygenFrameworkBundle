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
abstract class OxygenConfiguration implements ConfigurationInterface
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
    public function addEntityConfiguration($rootNode, $entity_class)
    {
    	if (is_null($this->entitiesNode))
    		$this->initNodeEntities($rootNode);
    	
    	$entity_parts = explode("\\", $entity_class);
    	$entityClassName = array_pop($entity_parts);
    	// Entity id
    	$entity_id = $this->camelCaseStringToUnderScores($entityClassName);
    	// Repository ?
    	$repository_class = join('\\', $entity_parts) . '\Repository\\' . $entityClassName . 'Repository';
    	if (!class_exists($repository_class)) {
    		$repository_class = 'Doctrine\ORM\EntityRepository';
    	}
    	// Manager ?
    	$manager_class = join('\\', $entity_parts) . '\Manager\\' . $entityClassName . 'Manager';
    	if (!class_exists($manager_class)) {
    		$manager_class = null;
    	}
    	// Table name
    	array_pop($entity_parts);
    	$tableName = preg_replace('/_bundle/', '', $this->camelCaseStringToUnderScores(join('', $entity_parts))) .'_'. $entity_id;
		
    	$this->entitiesNode
    		->children()
    			->arrayNode($entity_id)
    				->addDefaultsIfNotSet()
    				->children()
			        	->scalarNode('class')->defaultValue($entity_class)->end()
			        	->scalarNode('repository')->defaultValue($repository_class)->end()
			        	->scalarNode('manager')->defaultValue($manager_class)->end()
			        	->scalarNode('table_name')->defaultValue($tableName)->end()
			        ->end()
			    ->end()
	        ->end();
    }
}
