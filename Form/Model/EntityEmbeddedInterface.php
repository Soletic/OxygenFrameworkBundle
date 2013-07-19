<?php
namespace Oxygen\FrameworkBundle\Form\Model;

/**
 * Implement this interface if form model embed an entity
 * 
 * @author lolozere
 *
 */
interface EntityEmbeddedInterface {
	
	public function setEntity($entity);
	public function getEntity();
	public function getId();
	
}