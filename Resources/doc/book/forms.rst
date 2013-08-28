Formulaires
===========

Introduction
------------

Oxygen propose un cadre ayant pour objectif de :

* S'appuyer sur le mécanisme existant dans Symfony2 pour la de création de formulaires. Nous vous invitons à commencer par lire la `documentation Symfony2 sur les formulaires <http://symfony.com/doc/current/book/forms.html>`_;
* Encapsuler dans une classe l'ensemble du traitement d'un formulaire : chargement et  traitement après sousmission
* Faciliter la surcharge des formulaires pour les personnaliser
* Composer des formulaires à partir d'autres formulaires

 
Principes de fonctionnement
---------------------------

Chaque formulaire est un service taggué avec *oxygen.form* : 

.. code-block:: xml

   <service id="oxygen_passbook.form.event" class="MyClassForm">
      <tag name="oxygen.form" id="my_form_id" />
   </service>

Ces services ainsi taggués sont référencés dans un pool de formulaire par le framework :

.. code-block:: php

   $form = $this->get('oxygen_framework.form')->getForm('my_form_id');

*MyClassForm* doit hériter de la classe *Oxygen\FrameworkBundle\Form\Form*

L'utilisa

Avant tout long discours, donnons un exemple :

.. code-block:: php

   // src/You/SomethingBundle/Form/Handler/SimpleForm.php
   namespace You\SomethingBundle\Form\Handler
   
   use 
   
   class SimpleForm extends Form

Tout formulaire dans Oxygen hérite de la classe *Oxygen\FrameworkBundle\Form\Form*. En cré
