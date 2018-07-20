Setup
=====

This chapter guides you through a minimal setup. Further options like overriding
templates are discussed in later sections.

First, you have to add CRUDlex to your composer.json:


.. tabs::

   .. tab:: Symfony 4

      .. code-block:: bash

          composer require "philiplb/crudlexsymfony4bundle"

   .. tab:: Silex 2

      .. code-block:: bash

          composer require "philiplb/crudlexsilex2"

Then comes the actual setup. Currently, only MySQL is supported. Although the
database layer is kept in an abstract way, so in future more data stores will
come. It relies on the DoctrineServiceProvider which has to be initialized and
registered:

.. tabs::

   .. tab:: Symfony 4

      Todo

   .. tab:: Silex 2

      .. code-block:: php

          $app->register(new Silex\Provider\DoctrineServiceProvider(), [
              'dbs.options' => [
                  'default' => [
                      'host' => '<yourHost>',
                      'dbname' => '<yourDBName>',
                      'user' => '<yourDBUser',
                      'password' => '<yourDBPassword>',
                      'charset' => 'utf8',
                  ]
              ]
          ]);


Now follows the setup of CRUDlex itself. First of all, we create an instance
of the MySQLDataFactory, internally taking care of creating MySQL-DB-access
objects:

.. tabs::

   .. tab:: Symfony 4

      Todo

   .. tab:: Silex 2

      .. code-block:: php

          $dataFactory = new CRUDlex\MySQLDataFactory($app['db']);

Now it's time to register the ServiceProvider itself:

.. tabs::

   .. tab:: Symfony 4

      Todo

   .. tab:: Silex 2

      .. code-block:: php

          $app->register(new CRUDlex\Silex\ServiceProvider(), [
              'crud.file' => __DIR__ . '<yourCrud.yml>',
              'crud.datafactory' => $dataFactory
          ]);

The content of the crud.yml (or whatever you name it) will be discussed in the
next chapter.

Now it's time to mount the Controller:

.. tabs::

   .. tab:: Symfony 4

      Todo

   .. tab:: Silex 2

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
