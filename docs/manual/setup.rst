Setup
=====

This chapter guides you through a minimal setup. Further options like overriding
templates are discussed in later sections.

First, you have to add CRUDlex to your composer.json:


.. tabs::

   .. group-tab:: Symfony 4

      .. code-block:: bash

          composer require "philiplb/crudlexsymfony4bundle"

      One of the requirements is unfortunately not stable so this has to be added to your composer.json:

      .. code-block:: js

          "minimum-stability": "dev",
          "prefer-stable": true ,

   .. group-tab:: Silex 2

      .. code-block:: bash

          composer require "philiplb/crudlexsilex2"

Then comes the actual setup. Currently, only MySQL is supported. Although the
database layer is kept in an abstract way, so in future more data stores will
come. It relies on the DoctrineServiceProvider which has to be initialized and
registered:

.. tabs::

   .. group-tab:: Symfony 4

      Assuming you are using the .env configuration:

      .. code-block:: php

          DATABASE_URL=mysql://<yourDBUser>:<yourDBPassword>@<yourHost>/<yourDBName>

   .. group-tab:: Silex 2

      .. code-block:: php

          $app->register(new Silex\Provider\DoctrineServiceProvider(), [
              'dbs.options' => [
                  'default' => [
                      'host' => '<yourHost>',
                      'dbname' => '<yourDBName>',
                      'user' => '<yourDBUser>',
                      'password' => '<yourDBPassword>',
                      'charset' => 'utf8',
                  ]
              ]
          ]);


Now follows the setup of CRUDlex itself. First of all, we create an instance
of the MySQLDataFactory, internally taking care of creating MySQL-DB-access
objects:

.. tabs::

   .. group-tab:: Symfony 4

      This is already setup with the default DBAL connection within the services and can be overwritten within the
      *config/services.yaml*:

      .. code-block:: yaml

          crudlex.dataFactoryInterface:
              public: true
              class: "CRUDlex\\MySQLDataFactory"
              arguments:
                - "@doctrine.dbal.default_connection"

   .. group-tab:: Silex 2

      .. code-block:: php

          $dataFactory = new CRUDlex\MySQLDataFactory($app['db']);

Now it's time to register the Service itself:

.. tabs::

   .. group-tab:: Symfony 4

      This is the default setup of the service you are able to overwrite within the *config/services.yaml*:

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

   .. group-tab:: Silex 2

      .. code-block:: php

          $app->register(new CRUDlex\Silex\ServiceProvider(), [
              'crud.file' => __DIR__ . '<yourCrud.yml>',
              'crud.datafactory' => $dataFactory
          ]);

The content of the crud.yml (or whatever you name it) will be discussed in the
next chapter.

Now it's time to mount the Controller:

.. tabs::

   .. group-tab:: Symfony 4

      The routes have to be added to the *config/routes.yaml*:

      .. code-block:: php

          crudlex:
              resource: '@CRUDlexSymfony4Bundle/Resources/config/routes.yaml'
              prefix: /crud

   .. group-tab:: Silex 2

      .. code-block:: php

          $app->boot();
          $app->mount('/crud', new CRUDlex\Silex\ControllerProvider());

It has to happen after the application has been booted as some access to service providers happen inside.
And that's it. Your CRUD UI should be available now, for example if you
defined a book entity in the crud.yml::

    http://.../crud/book

You can override the used Controller instance in order to customize the behaviour. Simply extend from CRUDlex\Controller
and adjust or implement CRUDlex\ControllerInterface. Then instantiate your class and set $app['crud.controller'] with
it before mounting.
