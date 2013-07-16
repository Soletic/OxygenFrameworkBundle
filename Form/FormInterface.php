<?php
namespace Oxygen\FrameworkBundle\Form;

use Symfony\Component\Form\FormView;

use Oxygen\FrameworkBundle\Form\Model\FormModelInterface;

interface FormInterface {
	
	/**
	 * Return the string of dataClass
	 * 
	 * @return string
	 */
	public function getDataClass();
	/**
	 * Return the string of form type : an alias form type or a full qualified class
	 *
	 * @return string
	 */
	public function getType();
	
	/**
	 * Return the model of the Form
	 * 
	 * @return FormModelInterface
	 */
	public function getModel();
	/**
	 * Return true if form submitted
	 * 
	 * @return bool
	 */
	public function isSubmitted();
	/**
	 * Method call to create the Symfony Form
	 * 
	 * @return FormInterface
	 */
	public function createForm();
	/**
	 * Method call to create the form view
	 * 
	 * @return FormView
	 */
	public function createView(FormView $parent = null);
	/**
	 * Method call on load Form
	 * 
	 * @return FormInterface
	 */
	public function onLoad(array $params);
	/**
	 * Method call for processing the form
	 * 
	 * @return bool Return true if process successed
	 */
	public function process();
	/**
	 * Method call on submit
	 *
	 * @return bool Return true if submit successed
	 */
	public function onSubmit();
	/**
	 * Method call on success of process form
	 */
	public function onSuccess();
	
}