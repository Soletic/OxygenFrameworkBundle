<?php
namespace Oxygen\FrameworkBundle\Model;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;

/**
 * Class to serve the manager of an entity
 * 
 * @author lolozere
 *
 */
class EntitiesServer {
	
	/**
	 * Array of entity manager already built
	 * 
	 * @var array
	 */
	protected $entitiesManager = array();
	/**
	 * 
	 * @var Container
	 */
	protected $container = null;
	
	/**
	 * 
	 * @var DoctrineEntityManager
	 */
	protected $entityManager;
	
	public function __construct(DoctrineEntityManager $entityManager, $container)
	{
		$this->entityManager = $entityManager;
		$this->container = $container;
	}
	/**
	 * Return manager of an entity register has extensible
	 * 
	 * @param string $entity_path
	 * @throws \Exception entity_path bad-formed
	 * @throws \Exception Parameter class or repository for entity not defined in configuration
	 * @return EntityManager
	 */
	public function getManager($entity_path) {
		if (empty($this->entitiesManager[$entity_path])) {
			$entity_parts = explode('.', $entity_path);
			if (count($entity_parts) != 2) {
				throw new \Exception(sprintf('entity_path bad-formed (%s). Example of well-formed : oxygen_something.person for an entity Person in OxygenSomethingBundle', $entity_path));
			}
			if (!$this->container->hasParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.class')) {
				throw new \Exception(sprintf('Parameter %s not defined in configuration', $entity_parts[0] . '.entities.'.$entity_parts[1].'.class'));
			}
			if (!$this->container->hasParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.repository')) {
				throw new \Exception(sprintf('Parameter %s not defined in configuration', $entity_parts[0] . '.entities.'.$entity_parts[1].'.repository'));
			}
			$class = $this->container->getParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.class');
			$repository = $this->container->getParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.repository');
			$manager = $this->container->getParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.manager');
			// Manager exist ?
			if (!is_null($manager) && class_exists($manager)) {
				$manager = new $manager($class, $repository);
			} else {
				$manager = new EntityManager($class, $repository);
			}
			$manager->load($this->entityManager);
			$manager->setEventDispatcher($this->container->get('event_dispatcher'));
			$manager->setId($entity_path);
			$this->entitiesManager[$entity_path] = $manager;
		}
		return $this->entitiesManager[$entity_path];
	}
	/**
	 * Return true if manager exist for $entity_path
	 * 
	 * @param string $entity_path
	 * @return bool
	 */
	public function has($entity_path) {
		$entity_parts = explode('.', $entity_path);
		if (count($entity_parts) != 2) {
			return false;
		}
		if (!$this->container->hasParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.class')) {
			return false;
		}
		if (!$this->container->hasParameter($entity_parts[0] . '.entities.'.$entity_parts[1].'.repository')) {
			return false;
		}
		return true;
	}
}