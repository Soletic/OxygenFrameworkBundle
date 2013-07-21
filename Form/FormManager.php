<?php
namespace Oxygen\FrameworkBundle\Form;

use Symfony\Component\DependencyInjection\Container;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Form;

/**
 * Manager to process Form
 * 
 * @author lolozere
 *
 */
class FormManager implements FormManagerInterface {
	
	/**
	 * 
	 * @var Container
	 */
	protected $container;

	protected $forms = array();
	
	public function __construct($container) {
		$this->container = $container;
	}
	/**
	 * Return an instance of the form
	 * 
	 * @param string $formId
	 * @param array $params
	 * @return Form
	 */
	public function getForm($formId, array $params = array()) {
		if (empty($this->forms[$formId]))
			throw new \Exception(sprintf("Form id %s doesn't exist", $formId));
		
		return $this->container->get($this->forms[$formId])->onLoad($params)->createForm();
	}
	/**
	 * Set list of forms service by form id
	 * 
	 * @param array $formsService
	 * @return void
	 */
	public function setForms($formsService) {
		$this->forms = $formsService;
	}
	
}