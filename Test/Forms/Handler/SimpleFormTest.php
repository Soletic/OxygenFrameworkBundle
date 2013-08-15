<?php
namespace Oxygen\FrameworkBundle\Test\Forms\Handler;

use Oxygen\FrameworkBundle\Form\Form;

class SimpleFormTest extends Form {
	
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.Form::getData()
	 */
	public function getData() {
		return null;
	}
	
	
	/**
	 * @param array $params
	 * @return \Oxygen\FrameworkBundle\Form\FormInterface
	 */
	public function onLoad(array $params) {
		return $this;
	}
	
	
	/**
	 * 
	 */
	public function onSubmit() {
		return true;
	}

	
	/**
	 * 
	 */
	public function onSuccess() {
		return true;
	}
	
}