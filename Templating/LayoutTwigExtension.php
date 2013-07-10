<?php
namespace Oxygen\FrameworkBundle\Templating;

use Symfony\Component\DependencyInjection\Container;

/**
 * Extension twig to build template extensible
 * 
 * @author lolozere
 *
 */
class LayoutTwigExtension extends \Twig_Extension {
	
	/**
	 * 
	 * @var Container
	 */
	protected $container;
	
	/**
	 * Constructor
	 *
	 */
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	/**
	 * Retourne le nom de l'extension
	 *
	 * @see Twig_ExtensionInterface::getName()
	 */
	public function getName()
	{
		return 'oxygen_framework_twig_layout';
	}
	
	/**
	 * Déclaration des fonctions étendant Twig
	 *
	 * @see Twig_Extension::getFunctions()
	 */
	public function getFunctions() {
		return array(
				'oxygen_layout'          => new \Twig_Function_Method($this, 'layout'),
		);
	}
	/**
	 * Return main layout to use for a view
	 * 
	 * @return string
	 * @throws \Exception 
	 */
	public function layout($name = null) {
		$request = $this->container->get('request');
		if (!is_null($name) && $this->container->hasParameter('oxygen_framework.templating.layouts.'.$name)) {
			return $this->container->getParameter('oxygen_framework.templating.layouts.'.$name);
		} elseif (!is_null($name)) {
			throw new \Exception(sprintf('layout %s undefined in oxygen_framework configuration', $name));
		}
		if ($request->isXmlHttpRequest() && $this->container->hasParameter('oxygen_framework.templating.layouts.light')) {
			return $this->container->getParameter('oxygen_framework.templating.layouts.light');
		} elseif ($this->container->hasParameter('oxygen_framework.templating.layouts.full')) {
			return $this->container->getParameter('oxygen_framework.templating.layouts.full');
		}
		throw new \Exception("Layout undefined in oxygen_framework configuration");
	}
	
	
}