<?php
namespace Oxygen\FrameworkBundle\Model;

/**
 * Contains method to generate events by entity registered
 * 
 * @author lolozere
 */
class EntityEvents {
	
	public static function beforeRemove($entityId) {
		return $entityId. '_before_remove';
	}
	public static function afterRemove($entityId) {
		return $entityId. '_after_remove';
	}
	/**
	 * Event just after constructor of the instance
	 * 
	 * @param string $entityId
	 */
	public static function created($entityId) {
		return $entityId. '_created';
	}
	/**
	 * Event after construction of entity and when all datas loaded
	 * 
	 * @param string $entityId
	 */
	public static function initialized($entityId) {
		return $entityId. '_created';
	}
	
}