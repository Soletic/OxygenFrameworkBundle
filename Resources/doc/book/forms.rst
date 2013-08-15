Formulaires
===========

Introduction
------------

Oxygen propose un cadre ayant pour objectif de :

* S'appuyer sur le mécanisme existant dans Symfony2 pour la de création de formulaires. Nous vous invitons à commencer par lire
la `documentation Symfony2 sur les formulaires <http://symfony.com/doc/current/book/forms.html>`_;
* Encapsuler dans une classe l'ensemble du traitement d'un formulaire : chargement et  traitement après sousmission
* Faciliter la surcharge des formulaires pour les personnaliser
* Composer des formulaires à partir d'autres formulaires

 
Créer un formulaire
-------------------

Avant tout long discours, donnons un exemple :

.. code-block:: php

   // src/You/SomethingBundle/Form/Handler/SimpleForm.php
   namespace You\SomethingBundle\Form\Handler
   
   use 
   
   class SimpleForm extends Form

Tout formulaire dans Oxygen hérite de la classe *Oxygen\FrameworkBundle\Form\Form. En cré
