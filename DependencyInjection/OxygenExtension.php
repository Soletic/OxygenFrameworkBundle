<?php
namespace Oxygen\FrameworkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Base class to define Bundle Extension
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
abstract class OxygenExtension extends Extension
{
	/**
	 * Transform configuration in parameter according tree
	 * 
	 * @param ContainerBuilder $container
	 * @param string $rootNodeName Root name of the bundle. Example : oxygen_framework
	 * @param array $config
	 */
    public function mapsParameter(ContainerBuilder $container, $rootNodeName, $config) {
    	foreach($config as $name => $value) {
    		if (is_array($value)) {
    			$this->mapsParameter($container, $rootNodeName . '.' . $name, $value);
    		} else {
    			$container->setParameter($rootNodeName.'.'.$name, $value);
    		}
    	}
    }
    
    public function mapsEntitiesParameter(ContainerBuilder $container, $rootNodeName, $config) {
    	foreach($config as $name => $value) {
    		if ($name == 'entities') {
    			$this->mapsParameter($container, $rootNodeName . '.' . $name, $value);
    		}
    	}
    }
    
}
