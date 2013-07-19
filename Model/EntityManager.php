<?php
namespace Oxygen\FrameworkBundle\Model;

use Oxygen\FrameworkBundle\Model\Event\ModelEvent;

use Doctrine\ORM\EntityManager as EM;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Classe offrant des fonctions de gestion d'une entité
 * 
 * @author lolozere
 *
 */
class EntityManager extends ModelManager {
	
	/**
	 * @var EM
	 */
	protected $em;
	protected $repository;
	protected $repositoryClass;
	/**
	 * Id (or entity path) for the entity
	 * 
	 * @var string
	 */
	protected $id;
	
	/**
	 * 
	 * @var Doctrine\ORM\Mapping\ClassMetadata
	 */
	protected $meta;
	
	public function __construct($class, $repository) {
		$this->repositoryClass = $repository;
		parent::__construct($class);
	}
	/**
	 * Return id (or entity path) for the entity
	 * 
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	
	public function createInstance() {
		$class = $this->class;
		$entity = new $class();
		$this->dispatcher->dispatch(EntityEvents::created($this->getId()), new ModelEvent($entity));
		return $entity;
	}
	/**
	 * Remove entity from the database
	 * 
	 * @param mixed $entity
	 */
	public function remove($entity) {
		$this->dispatcher->dispatch(EntityEvents::beforeRemove($this->getId()), new ModelEvent($entity));
		$this->em->remove($entity);
		$this->dispatcher->dispatch(EntityEvents::afterRemove($this->getId()), new ModelEvent($entity));
	}
	/**
	 * Copy entity
	 *
	 * @param mixed $entity
	 */
	public function copy($entity) {
		throw new \Exception("Not implemented");
	}
	public function load($entityManager) {
		$this->em = $entityManager;
		$this->meta = $this->em->getClassMetadata($this->class);
		$matches = array();
		//Vérification si le repository est un parameter
		if (!is_null($this->repositoryClass)) {
			$this->meta->setCustomRepositoryClass($this->repositoryClass);
			$repositoryClassName = $this->meta->customRepositoryClassName;
			$this->repository = new $repositoryClassName($this->em, $this->meta);
		} else {
			$this->repository = $this->em->getRepository($this->class);
		}
	}
	/**
	 * @return Doctrine\ORM\Mapping\ClassMetadata
	 */
	public function getMetaData() {
		return $this->meta;
	}

	/**
	 * @return EntityRepository
	 */
	public function getRepository() {
		return $this->repository;
	}
}