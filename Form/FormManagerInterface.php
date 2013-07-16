<?php
namespace Oxygen\FrameworkBundle\Form;

/**
 * Interface for implementing a Form Manager
 * 
 * @author lolozere
 *
 */
interface FormManagerInterface {
	
	/**
	 * Return the form $formId declared by tag oxygen.form in a service
	 * 
	 * @param string $formId
	 * @param array $params
	 * @return FormInterface
	 */
	public function getForm($formId, array $params);
	
}