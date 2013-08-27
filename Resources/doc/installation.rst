Installation
============

Le bundles est actuellement dans une version beta (en cours de développement) et ne s'installe pas
en utilisant Composer pour le placer dans les vendor.

Vous pouvez

* Télécharger le code source au format ZIP : `Télécharger <https://github.com/Soletic/OxygenFrameworkBundle/archive/master.zip>`_
* Utiliser un client Git pour cloner le dépôt dans votre application

Le code source doit-être placé dans : /path/to/application/src/Oxygen/FrameworkBundle

Vous devez activer le bundle dans l'application :

.. code-block:: php

   <?php
   // app/AppKernel.php
   
   public function registerBundles()
   {
       $bundles = array(
           // ...
           new Oxygen\FrameworkBundle\OxygenFrameworkBundle(),
       );
   }

Cas d'une application hébergée sur GitHub
-----------------------------------------

Si votre application est hébergé sur GitHub (par exemple : `https://github.com/lolozere/SSNPassApplication`_), 
nous vous conseillons d'utiliser la méthode Git et de faire du dépôt OxygenFramework un sous module Git de celui de votre application :

:doc:`../../cookbook/github/git-symfony2-submodules#ajouter-le-depot-comme-sous-module-du-depot-de-l-application`
