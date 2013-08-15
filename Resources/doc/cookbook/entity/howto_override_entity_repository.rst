Comment surcharger la classe Repository d'une entité en particulier ?
=====================================================================

La classe Repository par défaut de toutes entités
-------------------------------------------------

La classe par défaut est Doctrine\ORM\EntityRepository

La classe Repository par défaut pour une entité
-----------------------------------------------

Dans le bundle Oxygen déclarant l'entité, si un dossier Repository se trouve au même niveau
que la classe PHP de l'entité et contenant une classe portant le nom l'entité suffixé par Repository alors cette classe
sera utilisée comme étant la classe Repository par défaut.

Par exemple dans le bundle OxygenIdentityCard, nous avons un manager IdentityRepository se trouvant dans Entity/Repository.


Compléter la classe Repository par défaut
-----------------------------------------

Si vous souhaitez ajouter des méthodes de recherche à la classe Repository par défaut, vous devez en créer et héritant de celle par défaut
pour ensuite la préciser dans le fichier de configuration :

.. code-block:: yaml

   oxygen_identity_card:
      entities:
         identity:
            repository: You\SomethingBundle\Entity\Repository\IdentityRepository
            
Le premier niveau est le nom du bundle ayant déclaré l'entité.

Le code de classe ressemblant à :

.. code-block:: php

   namespace You\SomethingBundle\Entity\Repository;
   
   class IdentityRepository extends Oxygen\IdentityCardBundle\Entity\Repository\IdentityRepository
   {
      public function findBoys() {
         ...
      }
   }
   
S'utilisant ensuite ainsi :

.. code-block:: php

   $boys = $this->container->get('oxygen_framework.entities').getManager('oxygen_identity_card.identity')->getRepository()->findBoys();
