Templating
==========

Créer des vues au layout variable
---------------------------------

Un layout est la vue principale de votre application. Par exemple l'affiche complet d'une page internet avec
ses menus, entête, pied de page.

Lors de la création de vues dans votre bundle, vous étendez généralement un layout se trouvant à la racine de votre bundle.
Par exemple dans un bundle YouSomethingBundle, vous créez une vue YouSomethingBundle::layout.html.twig et toutes les
vues utilisées par vos controllers en héritent.

.. code-block:: jinja

   {# YouSomethingBundle:Something:index.html.twig #}
   {% extends 'YouSomethingBundle::layout.html.twig' %}
   
   {% block you_something_content %} ... {% endblock %}

Imaginez maintenant que vous faites une dizaine de vues dans votre bundle.

Si un développeur souhaite intégrer votre bundle dans une application, il surcharge YouSomethingBundle::layout.html.twig
(voir `Surcharge de vues dans Symfony2 <http://symfony.com/fr/doc/current/book/templating.html#overriding-bundle-templates>`_). Cependant, ce développeur 
peut souhaiter intégrer certaines vues de votre bundle dans un layout allégé (dans le cas d'un appel ajax par exemple),
alors que pour d'autres, le layout complet est parfait.

OxygenFramework vous permet de contourner cette limitation pour créer des bundles dont les vues seront plus finement personnalisables.

Pour cela, il suffit d'étendre vos vues avec la fonction twig oxygen_layout()

.. code-block:: jinja

   {# YouSomethingBundle:Something:index.html.twig #}
   {% extends oxygen_layout() %}
   
   {% block you_something_content %} ... {% endblock %}
   
   
Puis dans le fichier de configuration de l'application vous fixez le layout à utiliser dans le cas allégé et dans le cas complet :

.. code-block:: yaml

   oxygen_framework:
      templating:
         layouts:
            full: 'UnBundle::layout.html.twig'
            light: 'UnBundle::layout-light.html.twig'

Le layout light n'est pas obligatoire. Dans ce cas, OxygenFramework utilisera toujours le complet (full)

Le layout light est utilisé automatiquement par OxygenFramework lorsqu'il détecte que la request est Ajax.

