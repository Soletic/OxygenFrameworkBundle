<?php
namespace Oxygen\FrameworkBundle\Locale;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Symfony\Component\HttpKernel\KernelEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listen request to set default locale
 * 
 * @author lolozere
 *
 */
class RequestEventListener implements EventSubscriberInterface {
	
	/**
	 * @var Session
	 */
	protected $session;
	
	public function __construct($session) {
		$this->session = $session;
	}
	
	public static function getSubscribedEvents() {
		return array(
				KernelEvents::REQUEST => 'onRequest',
			);
	}
	
	public function onRequest(GetResponseEvent $request) {
		Locale::setDefault($request->getRequest()->getLocale());
	}
	
}