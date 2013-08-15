<?php
namespace Oxygen\FrameworkBundle\Tests\Forms;

use Symfony\Component\HttpFoundation\Request;

use Oxygen\FrameworkBundle\Test\Forms\Handler\SimpleFormTest;

use Oxygen\FrameworkBundle\Test\WebTestCase;

class FormsTest extends WebTestCase
{
	
	public function testSimple()
	{
		$this->setExpectedException('\Exception');
		
		$client = self::createClientWithScopeRequest();
		// Create the form
		$form = new SimpleFormTest($client->getContainer()->get('request'));
		
		$this-> $form->onLoad(array())->createForm();
		
		
		if ($form->isSubmitted()) {
			if ($form->process()) {
				//return $this->redirect($this->generateUrl('oxygen_passbook_event_list'));
			}
		}
		//return $this->render('OxygenIdentityCardBundle:Forms:edit_identity.html.twig', array('form' => $form->createView()));*/
	}

}
