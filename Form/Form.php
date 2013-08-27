<?php
namespace Oxygen\FrameworkBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;

use Symfony\Component\Form\FormBuilder;

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
	 * Form builder of the form
	 * 
	 * @var FormBuilder
	 */
	private $formBuilder;
	
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
		
		if (!is_null($formType)) {
			
		}
	}
	/**
	 * 
	 * @param FormFactoryInterface $factory
	 */
	public function setFormFactory(FormFactoryInterface $factory) {
		if (is_null($this->formFactory)) {
			// Create form builder when form factory is set
			if (is_null($this->formType )) {
				$formType = 'form';
			} elseif (class_exists($this->formType)) {
				$formType = $this->getType();
				$formType = new $formType();
			} else {
				$formType = $this->getType();
			}
			$this->formBuilder = $factory->createBuilder($formType, null, $this->options);
		}
		$this->formFactory = $factory;
		
	}
	public function setContainer($container) {
		$this->container = $container;
	}
	/**
	 * @return FormBuilder
	 */
	public function getFormBuilder() {
		return $this->formBuilder;
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
	public function getData() {
		
	}
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
		$this->form = $this->formBuilder->setData($this->getData())->getForm()->handleRequest($this->request);
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