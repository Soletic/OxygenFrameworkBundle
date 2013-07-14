<?php
namespace Oxygen\FrameworkBundle\Templating;

/**
 * Container de messages flash
 * 
 * @author lolozere
 *
 */
class FlashMessagesTwigExtension extends \Twig_Extension {
	
	protected $session;
	/**
	 * @var \Twig_Environment
	 */
	protected $environment;
	
	public function __construct($session) {
		$this->session = $session;
	}
	
	public function initRuntime(\Twig_Environment $environment)
	{
		$this->environment = $environment;
	}
	
	/**
	 * Déclaration des fonctions étendant Twig
	 *
	 * @see Twig_Extension::getFunctions()
	 */
	public function getFunctions() {
		return array(
				'oxygen_flash_messages'          => new \Twig_Function_Method($this, 'flashMessages'),
		);
	}
	
	/**
	 * Retourne le nom de l'extension
	 *
	 * @see Twig_ExtensionInterface::getName()
	 */
	public function getName()
	{
		return 'oxygen_framework_flash_messages';
	}
	
	public function addError($message) {
		$this->session->getFlashBag()->add('error', $message);
	}
	
	public function addWarning($message) {
		$this->session->getFlashBag()->add('warning', $message);
	}
	
	public function addSuccess($message) {
		$this->session->getFlashBag()->add('success', $message);
	}
	
	public function addInfo($message) {
		$this->session->getFlashBag()->add('info', $message);
	}
	
	/**
	 * Return HTML of flash messages according to OxygenFrameworkBundle::flash_messages.html.twig
	 * 
	 * @return string
	 */
	public function flashMessages() {
		$template = $this->environment->loadTemplate('OxygenFrameworkBundle::flash_messages.html.twig');
		return $template->renderBlock('flash_messages', $this->environment->getGlobals());
	}
	
}