Setup
=====

This chapter guides you through a minimal setup. Further options like overriding
templates are discussed in later sections.

First, you have to add CRUDlex to your composer.json:

.. code-block:: js

    "philiplb/crudlex": "0.9.10"

Then comes the actual setup. Currently, only MySQL is supported. Although the
database layer is kept in an abstract way, so in future more data stores will
come. It relies on the DoctrineServiceProvider which has to be initialized and
registered:

.. code-block:: php

    $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'dbs.options' => array(
            'default' => array(
                'host' => '<yourHost>',
                'dbname' => '<yourDBName>',
                'user' => '<yourDBUser',
                'password' => '<yourDBPassword>',
                'charset' => 'utf8',
            )
        )
    ));

Now follows the setup of CRUDlex itself. First of all, we create an instance
of the MySQLDataFactory, internally taking care of creating MySQL-DB-access
objects:

.. code-block:: php

    $dataFactory = new CRUDlex\MySQLDataFactory($app['db']);

Now it's time to register the ServiceProvider itself:

.. code-block:: php

    $app->register(new CRUDlex\ServiceProvider(), array(
        'crud.file' => __DIR__ . '<yourCrud.yml>',
        'crud.datafactory' => $dataFactory
    ));

The content of the crud.yml (or whatever you name it) will be discussed in the
next chapter.

Now it's time to mount the Controller:

.. code-block:: php

    $app->mount('/crud', new CRUDlex\ControllerProvider());

And that's it. Your CRUD UI should be available now, for example if you
defined a book entity in the crud.yml::

    http://.../crud/book
