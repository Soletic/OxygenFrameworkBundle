Comment surcharger le manager d'une entité en particulier ?
===========================================================

Le manager par défaut de toutes entités
---------------------------------------

Le manager par défaut de toute entité est Oxygen\FrameworkBundle\Model\EntityManager

Le manager par défaut pour une entité
-------------------------------------

Dans le bundle Oxygen déclarant l'entité, si un dossier Manager se trouve au même niveau
que la classe PHP de l'entité et contenant une classe portant le nom l'entité suffixé par Manager alors cette classe
sera utilisée comme étant le manager par défaut.

Par exemple dans le bundle OxygenIdentityCard, nous avons un manager IdentityManager se trouvant dans Entity/Manager.


Le manager personnalisé pour une entité
---------------------------------------

Si vous souhaitez modifier le comportement du manager d'entité ou y ajouter des fonctions, vous pouvez
créer votre propre manager puis le préciser via le fichier de configuration :

.. code-block:: yaml

   oxygen_identity_card:
      entities:
         identity:
            manager: You\SomethingBundle\Entity\Manager\IdentityManager
            
Le premier niveau est le nom du bundle ayant déclaré l'entité.