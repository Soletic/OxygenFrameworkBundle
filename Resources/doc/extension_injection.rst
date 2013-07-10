Charger plus facilement la configuration d'un bundle
====================================================

Lorsque vous créez un arbre de configuration pour votre bundle, vous devez la charger en implémentant du code
dans la classe DependencyExtension/SometingBundleExtension de votre bundle. Généralement, c'est pour transformer
votre arbre en plusieurs paramètres de l'application.

En héritant de la classe OxygenExtension au lieu de celle fournie par défaut dans Symfony2 
(Symfony\Component\HttpKernel\DependencyInjection\Extension), vous disposez d'une méthode mapsParameter
effectuant cela. Exemple :

.. code-block:: php

   <?php
   namespace You\SometingBundle\DependencyInjection;
   
   use Symfony\Component\DependencyInjection\ContainerBuilder;
   use Symfony\Component\Config\FileLocator;
   use Symfony\Component\DependencyInjection\Loader;
   
   /**
    * This is the class that loads and manages your bundle configuration
    *
    * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
    */
   class YouSometingExtension extends OxygenExtension
   {
       /**
        * {@inheritDoc}
        */
       public function load(array $configs, ContainerBuilder $container)
       {
           $configuration = new Configuration();
           $config = $this->processConfiguration($configuration, $configs);
           
           $this->mapsParameter($container, 'oxygen_framework', $config);
   
           ...
       }
   }

Ainsi, si dans le fichier de configuration de l'application vous avez :

.. code-block:: yaml

   oxygen_framework:
      templating:
         layouts:
            full: '::base.html.twig'
      exceptions:
         manage: false
         
Les paramètres suivant seront créés :

.. code-block:: yaml

   oxygen_framework.templating.layouts.full: '::base.html.twig'
   oxygen_framework.exceptions.manage: false
   
Le deuxième argument de la fonction est la nom racine des paramètres à créer et le troisième un tableau de configuration.