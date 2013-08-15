<?php
namespace Oxygen\FrameworkBundle\Test;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as Base;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author lolozere
 */
abstract class WebTestCase extends Base
{
	
	protected $environment = 'test';
	protected $containers;
	protected $kernelDir;
	// 5 * 1024 * 1024 KB
	protected $maxMemory = 5242880;
	
	static protected function getKernelClass()
	{
		$dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : self::getPhpUnitXmlDir();
	
		list($appname) = explode('\\', get_called_class());
	
		$class = $appname.'Kernel';
		$file = $dir.'/'.strtolower($appname).'/'.$class.'.php';
		if (!file_exists($file)) {
			return parent::getKernelClass();
		}
		require_once $file;
	
		return $class;
	}
	
	/**
	 * Creates a Client with scope request activated
	 *
	 * @param array $options An array of options to pass to the createKernel class
	 * @param array $server  An array of server parameters
	 *
	 * @return Client A Client instance
	 */
	protected static function createClientWithScopeRequest(array $options = array(), array $server = array())
	{
		$client = self::createClient();
		// Activate scope request
		$client->getContainer()->enterScope('request');
		$client->getContainer()->set('request', new Request());
		return $client;
	}
	
	/**
	 * Creates a mock object of a service identified by its id.
	 *
	 * @param string $id
	 *
	 * @return \PHPUnit_Framework_MockObject_MockBuilder
	 */
	protected function getServiceMockBuilder($id)
	{
		$service = $this->getContainer()->get($id);
		$class = get_class($service);
		return $this->getMockBuilder($class)->disableOriginalConstructor();
	}
	
	/**
	 * Builds up the environment to run the given command.
	 *
	 * @param string $name
	 * @param array $params
	 *
	 * @return string
	 */
	protected function runCommand($name, array $params = array())
	{
		array_unshift($params, $name);
	
		$kernel = $this->createKernel(array('environment' => $this->environment));
		$kernel->boot();
	
		$application = new Application($kernel);
		$application->setAutoExit(false);
	
		$input = new ArrayInput($params);
		$input->setInteractive(false);
	
		$fp = fopen('php://temp/maxmemory:'.$this->maxMemory, 'r+');
		$output = new StreamOutput($fp);
	
		$application->run($input, $output);
	
		rewind($fp);
		return stream_get_contents($fp);
	}
	
}
