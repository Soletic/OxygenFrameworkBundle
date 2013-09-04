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

Présentation
++++++++++++

Chaque formulaire est un service taggué avec *oxygen.form* : 

.. code-block:: xml

   <service id="oxygen_passbook.form.event" class="MyClassForm">
      <tag name="oxygen.form" id="my_form_id" />
   </service>

La classe surlaquelle s'appuie le service (ici *MyClassForm*) doit hériter de la classe *Oxygen\FrameworkBundle\Form\Form*

Ces services ainsi taggués sont référencés dans un pool de formulaire permettant de le récupérer :

.. code-block:: php

   $form = $this->get('oxygen_framework.form')->getForm('my_form_id');

La classe du service, en héritant de la classe abstraite *Oxygen\FrameworkBundle\Form\Form* doit implémenter 3 méthodes :

* onLoad(array $params)
* onSubmit()
* onSuccess()

La méthode onLoad est appelée avant la création du formulaire et permet ainsi :

* d'ajouter des champs au formulaire
* charger des données pour pré-remplir les champs
* ...

La méthode onSubmit() traite les saisies de l'internaute (mise à jour de base de données, envoi d'emails, ...) et retourne vrai si le
traitement s'est correctement déroulé.

La méthode onSuccess() arrive après onSubmit() et est appelé uniquement si onSubmit() à renvoyer vrai. Vous pouvez par exemple
dans cette méthode :

* Faire un flush des entités
* Définir un message dans le FlashMessage
* ...

Un formulaire de contact
++++++++++++++++++++++++

Donnons un exemple avec un formulaire de contact demandant une adresse email, la saisie d'un message et envoie un message
à un responsable du site internet.

Nous créons le formulaire :

.. code-block:: php

   // src/You/SomethingBundle/Form/Handler/ContactForm.php
   namespace You\SomethingBundle\Form\Handler;
   use Symfony\Component\Validator\Constraints\NotBlank;
   use Oxygen\FrameworkBundle\Form\Form;
   class ContactForm extends Form {
   
      public function onLoad(array $params) {
         
         $this->getFormBuilder()->add(
               'email', 'email', array('required' => true, 'label' => 'Email', 'constraints' => new NotBlank())
            )->add(
               'message', 'textarea', array('required'=> true, 'label' => 'Message', 'constraints' => new NotBlank())
            );
         
         return $this;
      }
      
      public function onSubmit() {
         // sent email
         // ...
               
         return true;
      }
      
      public function onSuccess() {
         $data = $this->form->getData();
         $this->container->get('oxygen_framework.templating.messages')->addSuccess(sprintf(
            'Email from %s sent : %s', $data['email'], $data['message']
         ));
         return true;
      }
   }

Nous le déclarons comme un service :

.. code-block:: xml

   <service id="you_something.form.contact" class="You\SomethingBundle\Form\Handler\ContactForm">
      <tag name="oxygen.form" id="you_something_contact_form" />
   </service>
   
Utilisons le formulaire dans une controller :

.. code-block:: php
   
   // src/You/SomethingBundle/Controller/DefaultController.php
   namespace You\SomethingBundle\Controller;
   use Oxygen\FrameworkBundle\Controller\OxygenController;
   class DefaultController extends OxygenController
   {
       public function contactAction()
       {
         $form = $this->get('oxygen_framework.form')->getForm('you_something_contact_form');
         if ($form->isSubmitted()) {
            if ($form->process()) {
               // ok, you can redirect or set a message for displaying on the view
            }
         }
         return $this->render('YouSometingBundle:Default:contact.html.twig', array('form' => $form->createView()));
       }
   }
   
La vue du formulaire :

.. code-block:: jinja

   <div>
      <form method="post" {{ form_enctype(form) }}>
      {{ form_errors(form) }}
      {{ form_widget(form) }}
      {{ form_rest(form) }}
      <p>
         <input formnovalidate type="submit" value="Envoyer" class="btn btn-primary" />
      </p>
      </form>
   </div>

Classe de données
-----------------

Dans l'exemple précédent les données associées au formulaire sont sous la forme d'un tableau. Nous allons crééer une classe
pour encapsuler les données sous la forme d'attributs de la classe. En utilisant une classe de données associée au formulaire
nous pouvons :

* Utiliser une fichier de validation plutôt que de les coder dans le formulaire
* Réutiliser le formulaire pour l'étendre avec la classe de données (utilisation d'héritage basique)

Nous créons donc une classe model :

.. code-block:: php
   
   // src/You/SomethingBundle/Model/ContactModel.php
   namespace You\SomethingBundle\Model;

   class ContactModel {
      
      protected $email;
      
      protected $message;
      
      public function setEmail($email)
      {
          $this->email = $email;
          return $this;
      }
       
      public function getEmail()
      {
          return $this->email;
      }
      
      public function setMessage($message)
      {
          $this->message = $message;
          return $this;
      }
       
      public function getMessage()
      {
          return $this->message;
      }
      
   }
   
Volontairement, nous créons ce modèle dans le dossier Model du bundle car nous prévoyons de le ré-utiliser dans d'autres cadres
(comme les entités par exemple).

Nous créons la classe model spécifiquement dédiée au formulaire en étendant la classe que nous venons de créer :
 
.. code-block:: php
   
   // src/You/SomethingBundle/Form/Model/ContactFormModel.php
   namespace You\SomethingBundle\Form\Model;

   class ContactFormModel extends You\SomethingBundle\Model\ContactModel {}

Puis nous la précisons au niveau du service :

.. code-block:: xml

   <service id="you_something.form.contact" class="You\SomethingBundle\Form\Handler\ContactForm">
      <tag name="oxygen.form" id="you_something_contact_form" dataClass="You\SomethingBundle\Form\Model\ContactFormModel" />
   </service>
   
En reprenant l'exemple précédent, la méthode onSuccess du formulaire est maintenant :

.. code-block:: php
   
   public function onSuccess() {
      $data = $this->form->getData();
      $this->container->get('oxygen_framework.templating.messages')->addSuccess(sprintf(
            'Email from %s sent : %s', $data->getEmail(), $data->getMessage()
         ));
      return true;
   }

Classe de formulaires
---------------------

Les classes de formulaires sont des classes controlant la construction du formulaire, ce que nous faisons actuellement dans
la méthode onLoad().

Pour comprendre les classes de formulaire dans Symfony2, lire `Créer des classes de formulaire <http://symfony.com/fr/doc/current/book/forms.html#creer-des-classes-de-formulaire>`_

Dans notre exemple nous aurons :



