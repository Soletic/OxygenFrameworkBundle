Entités surchargeables
======================

OxygenFramework propose un mécanisme pour créer bundles basés sur des entités à persiter dans d'autres bundles.
L'objectif est de permettre à d'autres développeurs de bénéficier des fonctionnalités du bundle tout en pouvant étendre
les capacités des entités (ajout d'attributs, relations, repository associé, ...)

Par exemple, dans un bundle OxygenContact, nous pouvons définir une entité Person ayant pour attribut le prénom et 
le nom. L'ensemble du code de ce bundle utilise cette entité pour réaliser des formulaires, liste de personnes, ...

Pour bénéficier des fonctionnalités offertes par le bundle, un développeur persiste l'entité Person dans un autre bundle
de l'application (et y ajouter la date d'anniversaire s'il le souhaite par exemple)

*Définition de "persister"*

Persister une entité surchargeable consiste à déclarer auprès de Doctrine l'entité afin de créer la table associée dans la
base de données. Ceci se fait en déclarant l'entité et ses attributs dans le dossier config/doctrine via un fichier *.orm.xml

Créer le squelette d'entité surchargeable
-----------------------------------------

Pour l'exemple, nous allons considérer l'entité Person dans un bundle OxygenContact.

Nous vous conseillons de créer dans votre bundle l'interface PersonInterface et la classe abstraite PersonModel l'implémentant.
Puis vous créez une class Person dans le dossier Entity :

.. code-block:: php

   <?php
   namespace Oxygen\ContactBundle\Entity
   
   class Person extend  Oxygen\ContactBundle\Model\PersonModel {}

Vous ne devez pas dans votre bundle créer le fichier directement dans config/doctrine car vous empêcherez toutes possibilités
de surcharger l'entité ! Le bundle souhaitant utiliser les fonctionnalités de OxygenContact, effectuera une copie de ce fichier
dans le dossier config/doctrine dans un autre bundle.

Enfin, pensez à créer la classe Repository associée à l'entité Person

.. code-block:: php

   <?php
   namespace Oxygen\ContactBundle\Entity\Repository;

   use Doctrine\ORM\EntityRepository;

   class PersonRepository extends EntityRepository {}
   
Déclarer l'entité à persister
-----------------------------

Vous devez ajouter à la configuration de votre bundle la possibilité de configurer l'entité en 
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
           $this->addEntityConfiguration($rootNode, 'Oxygen\ContactBundle\Entity\Person', 'Oxygen\ContactBundle\Entity\Repository\PersonRepository');
           ...
       }
   }
   
Puis modifiez la classe DependencyInjection/OxygenContactBundle en :
* faisant hériter de OxygenExtension
* ajoutant un appel à mapEntitiesParamter()

.. code-block:: php

   ...
   use Oxygen\FrameworkBundle\DependencyInjection\OxygenExtension;
   
   class OxygenPassbookExtension extends OxygenExtension
   {
      public function load(array $configs, ContainerBuilder $container) {
         ...
         $this->mapsEntitiesParameter($container, 'oxygen_contact', $config);
         ...
      }
   }

A partir de là, pour rendre opérationnel l'entité dans l'application, il faut la persister.

Persister et étendre l'entité
-----------------------------

L'entité se persiste dans un autre bundle, par exemple YouOneBundle, en 
* créant une classe dans le dossier Entity 
* héritant de l'entité de base

.. code-block:: php

   <?php
   namespace You\OneBundle\Entity
   
   class Person extend  Oxygen\ContactBundle\Entity\Person {}

Puis créer le fichier Person.orm.xml dans le dossier config/doctrine :

.. code-block:: xml

   <!-- You\OneBundle\config\doctrine\person.orm.xml -->
   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                     http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
         <entity name="Oxygen\ContactBundle\Entity\Person" table="oxygen_contact_person" repository-class="%oxygen_person.entities.person.repository%">
            <id name="id" type="integer" column="id">
                  <generator strategy="AUTO" />
               </id>
            <field name="firstName" type="string" length="100" nullable="false" />
            <field name="lastName" type="string" length="100" nullable="false" />
         </entity>
   </doctrine-mapping>
   
Enfin, indiquer la nouvelle dans le fichier de configuration

.. code-block:: yaml

   oxygen_contact
      entities:
         person:
            class: You\OneBundle\Entity\Person
            repository: You\OneBundle\Entity\Repository\PersonRepository # Not required
   
Vous pouvez ainsi ajoutez des méthodes et attributs à votre entité Person (en pensant à les ajouter aussi dans le fichier Person.orm.xml)


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

Faciliter la persistence d'une entité
-------------------------------------

Un développeur vous remerciera mille fois si vous lui fournissez un fichier *.orm.xml de base. Nous
vous conseillons de créer son squelette dans le dossier config/entities de votre bundle. Par exemple :

.. code-block:: xml

   <?xml version="1.0" encoding="UTF-8"?>
   <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                     http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
         <entity name="%oxygen_contact.entities.person.class%" table="%table%" repository-class="%oxygen_person.entities.person.repository%">
            <id name="id" type="integer" column="id">
                  <generator strategy="AUTO" />
               </id>
            <field name="firstName" type="string" length="100" nullable="false" />
            <field name="lastName" type="string" length="100" nullable="false" />
         </entity>
   </doctrine-mapping>
   
Les attributs du tag <entity> sont codifiés :
* name : nommage similaire au à l'arbre de configuration de l'entité
* repository : nommage similaire au à l'arbre de configuration de l'entité
* table : %table%

En responsant cette pratique de nommage, votre bundle bénificiera du futur installateur automatisant 
la persistence des entités surchargeables

