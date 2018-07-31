Overriding Layouts
==================

Each CRUDlex page extends from a certain layout with the default
"@crud/layout.twig".

In most cases you don't want to use this standard layout coming with CRUDlex.
This chapter shows you how to define your own layout templates on various
levels.

First of all, you need to place the Twig-templates in a folder known by the
Twig service.

.. tabs::

   .. group-tab:: Symfony 4

      Add your path to the twig.yaml:

      .. code-block:: yaml

          twig:
            paths: ['%kernel.project_dir%/templates']

   .. group-tab:: Silex 2

      Assuming you have your templates in the folder
      __DIR__.'/../views', you initialize the Twig like this:

      .. code-block:: php

          $app->register(new Silex\Provider\TwigServiceProvider(), [
              'twig.path' => _ _DIR_ _.'/../views'
          ]);

A good starting point is the layout template coming with CRUDlex:
src/views/layout.twig

CRUDlex resolves the layout to use in an hierarchy. More specific layouts
override the general ones. The following subchapters are ordered in this
hierarchy, from general to specific.

------
Global
------

If you want to override the general layout of all CRUDlex pages, you set the
key "layout" template via the the setTemplate function of the provider:

.. tabs::

   .. group-tab:: Symfony 4

      Place a call to setTemplate within your crudlex.service definition:

      .. code-block:: yaml

          crudlex.service:
              public: true
              class: "CRUDlex\\Service"
              arguments:
                - "%kernel.project_dir%/config/crud.yml"
                - "%kernel.cache_dir%"
                - "@Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface"
                - "@translator"
                - "@crudlex.dataFactoryInterface"
                - "@crudlex.entityDefinitionFactoryInterface"
                - "@crudlex.fileSystem"
                - "@crudlex.entityDefinitionValidatorInterface"
              calls:
                - method: setTemplate
                  arguments: ['layout', 'myLayout.twig']

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud']->setTemplate('layout', 'myLayout.twig');

--------------
Single Actions
--------------

You can override the layout of single actions:

* create
* list
* show
* edit

Just prepend a dot and the desired action at the key when defining the layout for it, for
example the action "show":

.. tabs::

   .. group-tab:: Symfony 4

      Place a call to setTemplate within your crudlex.service definition:

      .. code-block:: yaml

          crudlex.service:
              public: true
              class: "CRUDlex\\Service"
              arguments:
                - "%kernel.project_dir%/config/crud.yml"
                - "%kernel.cache_dir%"
                - "@Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface"
                - "@translator"
                - "@crudlex.dataFactoryInterface"
                - "@crudlex.entityDefinitionFactoryInterface"
                - "@crudlex.fileSystem"
                - "@crudlex.entityDefinitionValidatorInterface"
              calls:
                - method: setTemplate
                  arguments: ['layout.showk', 'myShowLayout.twig']

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud']->setTemplate('layout.show', 'myShowLayout.twig');

---------------
Single Entities
---------------

To override the layout of a single entity, you prepend a dot and the desired
entity name at the key, for example for the book entity:

.. tabs::

   .. group-tab:: Symfony 4

      Place a call to setTemplate within your crudlex.service definition:

      .. code-block:: yaml

          crudlex.service:
              public: true
              class: "CRUDlex\\Service"
              arguments:
                - "%kernel.project_dir%/config/crud.yml"
                - "%kernel.cache_dir%"
                - "@Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface"
                - "@translator"
                - "@crudlex.dataFactoryInterface"
                - "@crudlex.entityDefinitionFactoryInterface"
                - "@crudlex.fileSystem"
                - "@crudlex.entityDefinitionValidatorInterface"
              calls:
                - method: setTemplate
                  arguments: ['layout.book', 'myBookLayout.twig']

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud']->setTemplate('layout.book', 'myBookLayout.twig');

---------------------------
Single Actions of an Entity
---------------------------

The most specific layout you can set is for a single action of a specific
entity. The key is "layout" plus a dot, the action, another dot and the entity. To
override the create action of the book entity, you would define your layout like this:

.. tabs::

   .. group-tab:: Symfony 4

      Place a call to setTemplate within your crudlex.service definition:

      .. code-block:: yaml

          crudlex.service:
              public: true
              class: "CRUDlex\\Service"
              arguments:
                - "%kernel.project_dir%/config/crud.yml"
                - "%kernel.cache_dir%"
                - "@Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface"
                - "@translator"
                - "@crudlex.dataFactoryInterface"
                - "@crudlex.entityDefinitionFactoryInterface"
                - "@crudlex.fileSystem"
                - "@crudlex.entityDefinitionValidatorInterface"
              calls:
                - method: setTemplate
                  arguments: ['layout.create.book', 'myCreateBookLayout.twig']

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud']->setTemplate('layout.create.book', 'myCreateBookLayout.twig');

----------------------------
Your own Layout from Scratch
----------------------------

If you want to start from scratch, it is recommended to use the layout coming
with CRUDlex as a starting point:
"vendor/philiplb/crudlex/src/views/layout.twig"

This one shows a menu with all defined entities linking to their list view.

First, you have to define a block called "content".
This is where CRUDlex renders itself into:

.. code-block:: twig

    {% block content %}{% endblock %}

In the head-section, you should include the template "@crud/header.twig":

.. code-block:: twig

    {% include '@crud/header.twig' %}

This one includes all needed CSS files like Bootstrap 3 and the CSS of the
datetime picker. Have a look at its content if you already include Bootstrap.

At the bottom of the page before the closing body tag, you include the template
"@crud/footer.twig":

.. code-block:: twig

    {% include '@crud/footer.twig' %}

It includes the JavaScript of:

* jQuery
* Bootstrap
* Moment
* Datetimepicker

Plus it initializes the datepickers, the datetimepickers and tooltips.

It's also recommended to include flashes in your own layout like this:

.. code-block:: twig

    {% if app.session.flashBag is defined %}
        {% set flashTypeAvailable = [ 'success', 'danger'] %}
        {% for flashType in flashTypeAvailable %}
            {% for flash in app.session.flashBag.get(flashType) %}
              <div class="alert alert-{{ flashType }}" >
                  <button class="close" data-dismiss="alert">×</button>
                  {{ flash }}
              </div>
            {% endfor %}
        {% endfor %}
    {% endif %}

And some CSS classes to implement:

.. code-block:: css

    .btn-crudlex {
        margin: 5px 5px 5px 5px;
    }
    .tooltip-crudlex {
        cursor: pointer;
    }
