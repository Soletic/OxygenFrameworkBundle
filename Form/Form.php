<?php
namespace Oxygen\FrameworkBundle\Form;

use Doctrine\Common\Collections\Collection;

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
	
	protected $options = array(
			'validation_groups' => array('default'),
		);
	
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
	public function __construct(Request $request, $formType = null, $dataClass = null) {
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
	abstract public function getData();
	/**
	 * Return the normalized model data
	 * 
	 * @return mixed
	 */
	public function getModel() {
		return $this->form->getNormData();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Oxygen\FrameworkBundle\Form.FormInterface::createForm()
	 */
	public function createForm() {
		if (is_null($this->getType())) {
			throw new \Exception('Not implemented');
		} else {
			if (class_exists($this->getType())) {
				$formType = $this->getType();
				$formType = new $formType();
			} else {
				$formType = $this->getType();
			}
			$this->form = $this->formFactory->create($formType, $this->getData(), $this->options)->handleRequest($this->request);
		}
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
		if ($this->form->isValid() && $this->onSubmit()) {
			return $this->onSuccess();
		}
		return false;
	}
	/**
	 * Return elements disapeared in the form collection
	 * 
	 * @param array $originals Original elements before submit
	 * @param Collection $elements Elements after submit
	 * @param bool $remove If true, remove the element from database
	 * @return array
	 */
	protected function getRemovedElement(array $originals, Collection $elements) {
		$entitiesRemoved = array();
		foreach($originals as $entity) {
			$removed = true;
			foreach($elements as $finalEntity) {
				if ($entity === $finalEntity) {
					$removed = false;
					break;
				}
			}
			if ($removed)
				$entitiesRemoved[] = $entity;
		}
		return $entitiesRemoved;
	}
}