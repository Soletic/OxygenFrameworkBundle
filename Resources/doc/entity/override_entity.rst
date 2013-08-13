Entités surchargeables
======================

Dans la documentation :doc:`play_with_entity`, vous avez pu découvrir comment utiliser le framework d'Oxygen
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
-------------------------------------------------------

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
--------------------------------------------

Dans le fichier de configuration de l'application, vous devez préciser la classe PHP utilisée pour chaque entité persistée.

Dans notre exemple nous aurons : 

.. code-block:: yaml

   oxygen_identity_card:
      entities:
         identity:
            class: You\SomethingBundle\Entity\Identity


