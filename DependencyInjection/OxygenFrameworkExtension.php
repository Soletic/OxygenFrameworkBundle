<?php

namespace Oxygen\FrameworkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OxygenFrameworkExtension extends OxygenExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $this->mapsParameter($container, 'oxygen_framework', $config);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services/templating.xml');
        $loader->load('services/model.xml');
        $loader->load('services/form.xml');
        $loader->load('services/locale.xml');
        
    }
}
