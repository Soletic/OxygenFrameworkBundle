<?php
namespace Oxygen\FrameworkBundle\Form;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\FormView;

use Symfony\Component\Form\Form as SymfonyForm;

use Symfony\Component\Form\FormFactory;

/**
 * Abstract class representing an Oxygen Form ready for processing
 * 
 * @author lolozere
 *
 */
abstract class Form implements FormInterface {
	
	protected $dataClass = null;
	protected $formType = null;
	
	protected $model = null;
	protected $params = array();
	protected $request = null;
	
	protected $container = null;
	
	/**
	 * @var SymfonyForm
	 */
	protected $form;
	
	/**
	 *
	 * @var FormFactory
	 */
	protected $formFactory;
	/**
	 * 
	 * @param Request $request
	 */
	public function __construct(Request $request, $formType, $dataClass) {
		$this->request = $request;
		$this->formType = $formType;
		$this->dataClass = $dataClass;
	}
	public function setFormFactory($factory) {
		$this->formFactory = $factory;
	}
	public function setContainer($container) {
		$this->container = $container;
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::getType()
	 */
	public function getType() {
		return $this->formType;
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::getDataClass()
	 */
	public function getDataClass() {
		return $this->dataClass;
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::isSubmitted()
	 */
	public function isSubmitted() {
		return $this->form->isSubmitted();
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::getModel()
	 */
	public function getModel() {
		if (is_null($this->model)) {
			$dataClass = $this->getDataClass();
			$this->model = new $dataClass();
		}
		return $this->model;
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::createForm()
	 */
	public function createForm() {
		$model = $this->getModel();
		if (class_exists($this->getType())) {
			$formType = $this->getType();
			$formType = new $formType();
		} else {
			$formType = $this->getType();
		}
		$this->form = $this->formFactory->create($formType, $model, array('validation_groups' => array('default')))->handleRequest($this->request);
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::createView()
	 */
	public function createView(FormView $parent = null) {
		return $this->form->createView($parent);
	}
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::process()
	 */
	public function process() {
		if ($this->onSubmit()) {
			return $this->onSuccess();
		}
		return false;
	}
	
}