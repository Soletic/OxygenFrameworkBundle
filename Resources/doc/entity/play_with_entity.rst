Jouer avec les entités
======================

Principes généraux
------------------

Chaque entité est identifié par un identifiant unique formé de la façon suivante :

*bundle_name.entity_name*

Par exemple, une entité se trouvant dans le bundle OxygenContactBundle et portant le nom PersonAdress,
l'identifiant sera : oxygen_contact.person_address

Pour chaque entité, un manager permet de la manipuler :

* Créer/supprimer une instance
* Accéder au repository
* Accéder aux metas-data, ...

Un service oxygen_framework.entities (serveur de manager d'entités) permet de récupérer le manager de l'entité :

.. code-block:: php
      
     $entity_manager = $this->container->get('oxygen_framework.entities')->getManager('oxygen_contact.person_address')

Définitions
-----------

*Persister*

Persister une entité consiste à déclarer auprès de Doctrine l'entité afin de créer la table associée dans la
base de données. Ceci se fait en déclarant l'entité et ses attributs dans le dossier config/doctrine via un fichier *.orm.xml

Créer une nouvelle entité
-------------------------

Créer le diagramme de classes d'une entité
++++++++++++++++++++++++++++++++++++++++++

Pour l'exemple, nous allons considérer l'entité Identity dans un bundle OxygenIdentityCard

Pour chaque entité, vous créez une interface et trois classes :

* Interface avec les méthodes get/set dans un dossier Model : IdentityInterface
* Une classe Model implémentant les méthodes dans le même dossier IdentityModel
* Une classe portant le nom de l'entité dans le dossier Entity : Identity
* Une classe Repository dans le sous-dossier Repository

Décrire pour doctrine l'entité
++++++++++++++++++++++++++++++

Oxygen recommande d'utiliser le format XML pour décrire une entité auprès de Doctrine. Ce fichier XML se place dans le
sous-dossier doctrine du dossier Ressources/config. Dans le cas de notre entité Identity, nous aurons :

Puis créer le fichier Person.orm.xml dans le dossier config/doctrine :

.. code-block:: xml

   <!-- @OxygenIdentityCardBundle\config\doctrine\person.orm.xml -->
   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                     http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
         <entity name="Oxygen\IdentityCardBundle\Entity\Identity" table="oxygen_idcard_identity" repository-class="%oxygen_identity_card.entities.identity.repository%">
            <id name="id" type="integer" column="id">
                  <generator strategy="AUTO" />
               </id>
            <field name="name" type="string" nullable="false" />
         </entity>
   </doctrine-mapping>
   
Le paramètre pour la classe Repository doit respecter la forme suivante en minuscule séparé par des underscore :

[nom du bundle].entities.[nom de l'entité].repository

Référencer l'entité au sein du bundle
+++++++++++++++++++++++++++++++++++++

Pour référencer une entité vous devez compléter l'arbre de configuration du bundle :

* faisant hériter la classe DependencyInjection/Configuration par la classe OxygenConfiguration disponible dans le framework
* ajoutant un appel à addEntityConfiguration()

.. code-block:: php

   ...
   use Oxygen\FrameworkBundle\DependencyInjection\OxygenConfiguration;
   
   class Configuration extends OxygenConfiguration implements ConfigurationInterface
   {
       
       public function getConfigTreeBuilder()
       {
           ...           
           $this->addEntityConfiguration($rootNode, 'Oxygen\IdentityCardBundle\Entity\Identity', 'Oxygen\IdentityCardBundle\Entity\Repository\IdentityRepository');
           ...
       }
   }
   
Ainsi, l'arbre de configuration du bundle est enrichi des noeuds suivants :

.. code-block:: yaml

   oxygen_identity_card
      entities:
         identity:
            class: ...
            repository: ...
   
Puis dans la classe extension du bundle, vous devez lire cette configuration afin de la transformer en paramètres globaux.
Pour cela, la classe *OxygenIdentityCardExtension* doit :

* hériter de OxygenExtension
* ajouter un appel à mapEntitiesParameter() dans la méthode load()

.. code-block:: php

   ...
   use Oxygen\FrameworkBundle\DependencyInjection\OxygenExtension;
   
   class OxygenPassbookExtension extends OxygenExtension
   {
      public function load(array $configs, ContainerBuilder $container) {
         ...
         $this->mapsEntitiesParameter($container, 'oxygen_identity_card', $config);
         ...
      }
   }

Il est donc possible maintenant de faire :

.. code-block:: php

   $entityClass = $this->container->getParameter('oxygen_identity_card.entities.identity.class');
   $entityManager = $this->container->get('oxygen_framework.entities')->getManager('oxygen_identity_card.identity')


Mettre à jour la base de données
++++++++++++++++++++++++++++++++

Il existe plusieurs façons de mettre à jour la base de données : 

* Soit en utilisant DoctrineMigrations
* Soit en forçant la mise à jour de la structure

Ici nous forçons la mise à jour de la structure :

.. code-block:: bash
   
   cd /path/to/application
   php app/console doctrine:schema:update --force

Manipuler l'entité avec un manager
----------------------------------

Doctrine fournit ce que l'on appelle l'EntityManager. Ce service permet de gérer la persistence de l'ensemble des entités
manipulés dans l'application.

Oxygen fournit un manager permettant de réaliser les manipulations de base sur une entité :

* création d'une instance
* suppression
* utilisation du repository 

Une instance de manager existe pour chaque entité. Par défaut, le manager est celui fournit par OxygenFramework.

Il ne se substitut pas à l'EntityManager de Doctrine mais offre une façon de manipuler une entité sans *jamais
nommé la classe* tout en générant des évènements associés (création, suppression).

Accéder au manager d'une entité
+++++++++++++++++++++++++++++++


Créer une instance d'une entité
+++++++++++++++++++++++++++++++

Rechercher des informations sur une entité (Repository)
+++++++++++++++++++++++++++++++++++++++++++++++++++++++




Evènements
----------



Manipuler l'entité avec le service oxygen_framework.entities
------------------------------------------------------------

Le but est d'ensuite de manipuler l'entité (créer un instance, faire une recherche) sans jamais utiliser directement le nom de la classe
de façon à ce que si l'entité est surchargé via une autre classe alors le code de votre bundle continue de fonctionner quelque soit
l'application où il est intégré.

Pour cela nous utilisons le service oxygen_framework.entities permettant d'accéder à un manager d'une entité :

.. code-block:: php
      
      $this->get('oxygen_framework.entities')->getManager('oxygen_contact.person')

*oxygen_contact.person* est un alias créé automatiquement par le framework et se compose deux parties :

* oxygen_contact : le nom racine de la configuration du bundle
* person : le nom de l'entité en minuscule

Un manager d'entité vous permet ensuite de retrouver le nom de la classe représentant l'entité et d'accéder au Repository :

.. code-block:: php
      
      $this->get('oxygen_framework.entities')->getManager('oxygen_contact.person')->getClassName();
      $persons = $this->get('oxygen_framework.entities')->getManager('oxygen_contact.person')->getRepository()->findAll();

