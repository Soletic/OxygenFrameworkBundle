Entités
=======

Introduction
------------

Principes généraux
++++++++++++++++++

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
+++++++++++

*Persister*

Persister une entité consiste à déclarer auprès de Doctrine l'entité afin de créer la table associée dans la
base de données. Ceci se fait en déclarant l'entité et ses attributs dans le dossier config/doctrine via un fichier \*.orm.xml

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

.. code-block:: xml

   <!-- @OxygenIdentityCardBundle\config\doctrine\Identity.orm.xml -->
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

.. _reference_entity:

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
           $this->addEntityConfiguration($rootNode, 'Oxygen\IdentityCardBundle\Entity\Identity');
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


Manager une entité
------------------

Doctrine fournit ce que l'on appelle l'EntityManager. Ce service permet de gérer la persistence de l'ensemble des entités
manipulés dans l'application.

Oxygen fournit un manager permettant de réaliser les manipulations de base sur une entité :

* création d'une instance
* suppression
* utilisation du repository 

Une instance de manager existe pour chaque entité. Par défaut, le manager est celui fournit par OxygenFramework.

Il ne se substitut pas à l'EntityManager de Doctrine mais offre une façon de manipuler une entité sans *jamais
nommé la classe* tout en générant des évènements associés (création, suppression).

Accéder au manager Oxygen d'une entité
++++++++++++++++++++++++++++++++++++++

L'accès au manager d'une entité se fait grâce au service %oxygen_framework.entities% :

.. code-block:: php

   $entityManager = $this->container->get('oxygen_framework.entities')->getManager('oxygen_identity_card.identity');
   
La méthode getManager prend en argument l'identifiant de l'entité. Cet identifiant est créé automatiquement lorsque l'on
référence l'entité : :ref:`reference_entity`

Créer une instance d'une entité
+++++++++++++++++++++++++++++++

Pour créer une nouvelle instance, le manager propose la méthode createInstance :

.. code-block:: php

   $entityManager = $this->container->get('oxygen_framework.entities')->getManager('oxygen_identity_card.identity');
   $identity = $entityManager->createInstance();
   
La création d'une nouvelle instance via le manager déclenche un évènement d'entité. Lire la section sur les évènements : :ref:`event_entity`

..

   Pour profiter complètement du système d'évènement, nous vous invitons donc à toujours utiliser le manager pour créer
   ou supprimer une entité.

Rechercher des informations sur une entité (Repository)
+++++++++++++++++++++++++++++++++++++++++++++++++++++++

Le Repository de chaque entité est accessible via la méthode getRepository() du manager :

.. code-block:: php

   $entityManager = $this->container->get('oxygen_framework.entities')->getManager('oxygen_identity_card.identity');
   $identityRepository = $entityManager->getRepository();
   $allIdentities = $identityRepository->findAll();
   
Entités surchargeables
----------------------

Dans la section :ref:`reference_entity`, vous avez pu découvrir comment utiliser le framework d'Oxygen
afin de manipuler les entités.

En ayant respectant ces pratiques, vous pouvez aller plus loin pour faire en sorte que vos entités
soit surchargeables par d'autres bundles. 

.. 
   Par exemple, dans un bundle OxygenIdentityCard, nous pouvons définir une entité Identity 
   ayant pour attribut le nom. L'ensemble du code de ce bundle utilise cette entité pour réaliser des formulaires, 
   liste de personnes, ...
   
   Pour bénéficier des fonctionnalités offertes par le bundle, un développeur persiste l'entité Identity dans un autre bundle
   de l'application (et y ajouter son surnom s'il le souhaite par exemple)
  
Créer et installer un fichier xml exemple pour l'entité
+++++++++++++++++++++++++++++++++++++++++++++++++++++++

Tout bundle Oxygen doit créer le fichier \*.orm.xml dans un dossier *entities* à la place de *doctrine* : Resources/config/entities.
Ce fichier est ensuite copié par le développeur utilisant votre bundle dans un dossier Resources/config/doctrine permettant à
Doctrine de la détecter et ainsi y associer une table dans la base de données.

Ce fichier XML doit utiliser une annotation %mon_parametre% pour préciser la classe entité et Repository PHP associées
et le nom de la table. Exemple :

.. code-block:: xml

   <!-- @OxygenIdentityCardBundle\config\entities\Identity.orm.xml -->
   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                     http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
         <entity name="%oxygen_identity_card.entities.identity.class%" table="%oxygen_identity_card.entities.identity.table_name%" repository-class="%oxygen_identity_card.entities.identity.repository%">
            <id name="id" type="integer" column="id">
                  <generator strategy="AUTO" />
               </id>
            <field name="name" type="string" nullable="false" />
         </entity>
   </doctrine-mapping>
   
Les %oxygen_identity_card.entities.identity.*% reprennent le même format que l'arbre de configuration des entités d'un bundle.

Ensuite, pour persister l'entité, dans un autre bundle, vous devez :

* Créer une classe PHP associée et se trouvant à la racine du dossier Entity (et héritant de celle de votre bundle)
* Copier le fichier ORM précédent dans le dossier Resources/config/doctrine
* Remplacer les %oxygen_identity_card.entities.identity.* par leurs valeurs (sauf pour repository-class ou ce n'est pas nécessaire).

..
   *Exemple*
   Imaginons que vous avez créé un bundle You/SomethingBundle. Pour persister l'entité Identity d'un bundle Oxygen, vous : 
   * devez créer une classe Identity dans le dossier Identity de votre bundle
   * copiez le fichier Resources/entities/identity.orm.xml dans votre un dossier Resources/doctrine de votre bundle
   * modifiez les %oxygen_identity_card.entities.identity.*% se trouvant dans ce fichier copié :
      * %oxygen_identity_card.entities.identity.class% : You/SomethingBundle/Entity/Identity
      * %oxygen_identity_card.entities.identity.table_name% : you_something_identity


Ces manipulations sont rendues obligatoires par le fonctionnement même de Doctrine dans Symfony2 car :

* Le fichier ORM d'une entité doit-être dans le même bundle que la classe PHP associée
* La classe PHP associée doit-être à la racine dans le dossier Entity du bundle

..
   *A savoir*
   En respectant cette notation, les entités de votre bundle pourront être traitées par le futur installateur automatique des
   entités.

Configurer la classe PHP associée à l'entité
++++++++++++++++++++++++++++++++++++++++++++

Dans le fichier de configuration de l'application, vous devez préciser la classe PHP utilisée pour chaque entité persistée.

Dans notre exemple nous aurons : 

.. code-block:: yaml

   oxygen_identity_card:
      entities:
         identity:
            class: You\SomethingBundle\Entity\Identity




.. _event_entity:

Evènements
----------

Pour chaque manipulation d'une entité via le manager, un évènement est lancé permettant de l'attraper afin de compléter le traitement.
Par exemple dans le cas d'une suppression, nous pouvons vérifier si nous avons le droit de la supprimer.

..

   Doctrine propose déjà des évènements comme prePersist, preRemove, ... que l'on peut attraper en créant un service
   les écoutant. Cependant ces services seront TOUS déclenchés puis il faut contrôler la nature de l'entité afin de déterminer
   si nous faisons un traitement ou pas. Les performances ne sont donc pas optimales.
   
   C'est pour cela qu'Oxygen, via le manager, permet de cibler les évènements pour chacune des entités.
   
Dans Symfony2, pour attraper des évènements, il faut créer un service implémentant l'interface EventSubscriberInterface, obligeant à
implémenter la méthode statique getSubscribedEvents(). Cette méthode renvoie un tableau dont la clé est l'identiant de l'évènement et
la valeur la méthode associée. (`Souscripteur d'évènement dans Symfony2 <http://symfony.com/fr/doc/current/components/event_dispatcher/introduction.html#utiliser-les-souscripteurs-d-evenement>`_)

OxygenFramework proposer une classe EntityEvents constituée de 3 méthodes statiques retournant un identifiant unique d'évènement pour 
chaque entité :

* beforeRemove($entityId) : évènement avant suppression d'une entité ayant pour id $entityId
* afterRemove($entityId) : évènement après suppression d'une entité ayant pour id $entityId
* created($entityId) : évènement après création d'une entité ayant pour id $entityId

Pour les évènements de mise à jour, il est trop complexe de surcharger aujourd'hui Doctrine permettant d'offrir ce genre d'évènement.

Par exemple, si nous souhaitons écouter la suppression d'une entité, ici oxygen_identity_card.identity, nous créons la classe
du service :

.. code-block:: php

   <?php
   use Symfony\Component\EventDispatcher\EventSubscriberInterface;
   use Oxygen\FrameworkBundle\Model\Event\ModelEvent;
   
   class EventsEventListener implements EventSubscriberInterface {
   
      public static function getSubscribedEvents() {
         return array(
               EntityEvents::beforeRemove('oxygen_identity_card.identity') => 'onRemove',
            );
      }
      
      public function onRemoveEventProduct(ModelEvent $event) {
         $entity = $event->getModel();
         ...
      }
   
   }
   
Puis nous déclarons le service :

.. code-block:: xml

   <service id="oxygen_identity_card.identity_listener" class="Oxygen\IdentityCardBundle\EventListener\Entity\IdentityListener">
      <tag name="kernel.event_subscriber" />
   </service>

