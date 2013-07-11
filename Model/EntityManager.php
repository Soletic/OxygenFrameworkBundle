<?php
namespace Oxygen\FrameworkBundle\Model;

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
	 * 
	 * @var Doctrine\ORM\Mapping\ClassMetadata
	 */
	protected $meta;
	
	public function __construct($class, $repository) {
		$this->repositoryClass = $repository;
		parent::__construct($class);
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