<?php
namespace Oxygen\FrameworkBundle\Model;

use Symfony\Component\Form\Form;

/**
 * Manager for model class
 * 
 * @author lolozere
 *
 */
abstract class ModelManager {
	
	protected $class;
	
	public function __construct($class) {	
		$this->class = $class;
	}
	
	public function getClassName() {
		return $this->class;
	}
	
	public function createInstance() {
		$class = $this->class;
		return new $class();
	}
	
	private function getBundleNamespace($namespace) {
		$matches = array();
		if (preg_match('/^(.+Bundle)/', $namespace, $matches)) {
			return $matches[1];
		}
		return null;
	}
	
	private function getClassNameWithoutNamespace($class) {
		$class_name_parts = explode('\\', $class);
		return end($class_name_parts);
	}
	
}