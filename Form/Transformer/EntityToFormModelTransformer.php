<?php
namespace Oxygen\FrameworkBundle\Form\Transformer;

use Doctrine\Common\Collections\Collection;

use Oxygen\FrameworkBundle\Form\Model\EntityEmbeddedInterface;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Tranformer data class Entity to this form model
 * 
 * @author lolozere
 *
 */
class EntityToFormModelTransformer implements DataTransformerInterface {

	protected $modelClass;
	protected $entityClass;
	
	public function __construct($modelClass, $entityClass) {
		$this->modelClass = $modelClass;
		$this->entityClass = $entityClass;
	}
	
	protected function transfer($from, $to) {
		$fromReflection = new \ReflectionClass(get_class($from));
		$toReflection = new \ReflectionClass(get_class($to));
		// Collection
		$attributesCollection = array();
		foreach($fromReflection->getProperties() as $fromProperty) {
			if ($toReflection->hasProperty($fromProperty->name)) {
				$fromProperty->setAccessible(true);
				$toProperty = $toReflection->getProperty($fromProperty->name);
				$toProperty->setAccessible(true);
				$toProperty->setValue($to, $fromProperty->getValue($from));
			}
		}
		// Get attributes
		$attributesFound = array();
		foreach($toReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $toMethodsSet) {
			$attributes = array();
			if (preg_match('/^set(?P<name>.+)/', $toMethodsSet->name, $attributes) && !in_array(lcfirst($attributes['name']), $attributesCollection)) {
				try {
					$fromMethodGet = $fromReflection->getMethod('get'.$attributes['name']);
					$attributesFound[] = $attributes['name'];
				} catch(\ReflectionException $e) {
					continue;
				}
				$set = true;
				if (is_null($getValue = $fromMethodGet->invoke($from))) {
					$parameters = $toMethodsSet->getParameters();
					$setArgument = current($parameters);
					$set = $setArgument->allowsNull();
				}
				if ($set) {
					$toMethodsSet->invokeArgs($to, array($getValue));
				}
			}
		}
		
	}
	
	/**
	 * Transforms an entity to a form model
	 *
	 * @param  Entity|null $entity
	 * @return string
	 */
	public function transform($entity)
	{
		if (null === $entity) {
			return null;
		}
		
		// Create model
		$modelClass = $this->modelClass;
		$model = new $modelClass();
		if (!($model instanceof EntityEmbeddedInterface)) {
			throw new \Exception(sprintf("%s must implement EntityEmbeddedInterface to transform in his form model", get_class($model)));
		}
		
		$this->transfer($entity, $model);
		$model->setEntity($entity);
		return $model;
	}
	
	/**
	 * Transforms the model in an entity
	 *
	 * @param  string $number
	 * @return Issue|null
	 * @throws TransformationFailedException if object (issue) is not found.
	 */
	public function reverseTransform($model)
	{
		$entity = $model->getEntity();
		// Can be null if it's data added by embedded collection form
		if (is_null($entity)) {
			$entityClass = $this->entityClass;
			$entity = new $entityClass();
		}
		$this->transfer($model, $entity);
		return $entity;
	}
	
}