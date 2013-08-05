Injection de dépendance
=======================

Oxygen a créé deux classes abstraites pour faciliter l'injection de dépendance :

* OxygenConfiguration : pour la configuration du bundle
* OxygenExtension : pour le chargement du bundle

Si vous n'êtes pas familié avec l'injection de dépendance, nous vous invitons à lire :

:doc:`http://symfony.com/fr/doc/current/cookbook/bundles/extension.html`

Configuration du bundle
-----------------------

Tout bundle a un dossier DependencyInjection avec à l'intérieur la classe *Configuration* implémentant la classe
*Symfony\Component\Config\Definition\ConfigurationInterface*.

Remplacer cette implémentation par l'héritage de la classe abstraite OxygenConfiguration. Ainsi vous bénéficiez
de méthodes vous simplifiant :

* La création de l'arbre de configuration du bundle
* La déclaration des entités (voir section :doc:`../entity/play_with_entity`)

Chargement des extensions du bundle
-----------------------------------

La chargement du bundle passe par une classe se trouvant dans le dossier DependencyInjection et s'appelant *NomBundleExtension*.
C'est ici que l'on lit l'arbre de configuration, charge les fichiers de services, ...

La classe abstraite *OxygenExtension* dont vous pouvez hériter faire hériter *NomBundleExtension* vous offre des méthodes
facilitant :

* La transformation de chaque élément de l'arbre de configuration en un paramètre globale : mapsParameter(...)
* ...

Exemple si votre bundle a comme arbre de configuration : 

.. code-block:: yaml

   you_something:
      templating:
         layouts:
            full: '::base.html.twig'
       something: [1,2]
       
En héritant de la classe OxygenExtension au lieu de celle fournie par défaut dans Symfony2 
(Symfony\Component\HttpKernel\DependencyInjection\Extension), vous disposez de la méthode mapsParameter. Exemple :

.. code-block:: php

   <?php
   namespace You\SometingBundle\DependencyInjection;
   
   use Symfony\Component\DependencyInjection\ContainerBuilder;
   use Symfony\Component\Config\FileLocator;
   use Symfony\Component\DependencyInjection\Loader;

   class YouSomethingExtension extends OxygenExtension
   {
       
       public function load(array $configs, ContainerBuilder $container)
       {
           $configuration = new Configuration();
           $config = $this->processConfiguration($configuration, $configs);
           
           $this->mapsParameter($container, 'you_something', $config);
   
           ...
       }
   }

Le deuxième argument de la fonction est le préfixe de chaque élément rencontré dans le tableau $config
         
Les paramètres globaux suivant seront créés :

.. code-block:: yaml

   you_something.templating.layouts.full: '::base.html.twig'
   you_something.something: [1,2]
   
