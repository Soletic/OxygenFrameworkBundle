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
			$manager = new EntityManager($class, $repository);
			$manager->load($this->entityManager);
			$this->entitiesManager[$entity_path] = $manager;
		}
		return $this->entitiesManager[$entity_path];
	}
	
}