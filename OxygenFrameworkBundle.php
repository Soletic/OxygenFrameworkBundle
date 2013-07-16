<?php
namespace Oxygen\FrameworkBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Oxygen\FrameworkBundle\DependencyInjection\Compiler\FormCompilerPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OxygenFrameworkBundle extends Bundle
{
	
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
		$container->addCompilerPass(new FormCompilerPass());
	}
	
}
