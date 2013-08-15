<?php
namespace Oxygen\FrameworkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Etend le bundle OxygenDiffusion
 * 
 * @author lolozere
 *
 */
class FormCompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		
		$pool = $container->getDefinition('oxygen_framework.form');
		
		$formsService = array();
		
		// Search tag
		$forms = $container->findTaggedServiceIds('oxygen.form');
		
		// Forms
		$attributesRequired = array('id');
		foreach ($forms as $id => $tagAttributes) {
			if (count($tagAttributes) > 1)
				throw new \Exception("Too much tag oxygen.form on service " . $id);
			
			$attributes = $tagAttributes[0];
			
			// Attribute required
			foreach($attributesRequired as $attribute) {
				if (empty($attributes[$attribute]))
					throw new \Exception(sprintf("Attribute %s required for tag oxygen.form in service %s", $attribute, $id));
			}
			
			$definition = $container->getDefinition($id)->addArgument(new Reference('request'))
				->addMethodCall('setFormFactory', array(new Reference('form.factory')))
				->addMethodCall('setContainer', array(new Reference('service_container')))
				->setScope('request');
			if (!empty($attributes['formType']))
				$definition->addArgument($attributes['formType']);
			if (!empty($attributes['dataClass']))
				$definition->addArgument($attributes['dataClass']);
			
			$formsService[$attributes['id']] = $id;
		}
		
		$pool->addMethodCall('setForms', array($formsService));
	}
}