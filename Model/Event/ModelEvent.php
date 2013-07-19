<?php
namespace Oxygen\FrameworkBundle\Model\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event on a model data
 * 
 * @author lolozere
 *
 */
class ModelEvent extends Event {
	
	protected $model;
	
	public function __construct($model) {
		$this->model = $model;
	}
	
	public function getModel() {
		return $this->model;
	}
	
}