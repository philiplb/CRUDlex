Setup
=====

This chapter guides you through a minimal setup. Further options like overriding
templates are discussed in later sections.

First, you have to add CRUDlex to your composer.json:

```json
"philiplb/crudlex": "0.9.8"
```

Then comes the actual setup. Currently, only MySQL is supported. Although the
database layer is kept in an abstract way, so in future more data stores will
come. It relies on the DoctrineServiceProvider which has to be initialized and
registered:

```php
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
```

Now follows the setup of CRUDlex itself. First of all, we create an instance
of the CRUDMySQLDataFactory, internally taking care of creating MySQL-DB-access
objects:

```php
$dataFactory = new CRUDlex\CRUDMySQLDataFactory($app['db']);
```

Now it's time to register the CRUDServiceProvider itself:

```php
$app->register(new CRUDlex\CRUDServiceProvider(), array(
    'crud.file' => __DIR__ . '<yourCrud.yml>',
    'crud.datafactory' => $dataFactory
));
```

The content of the crud.yml (or whatever you name it) will be discussed in the
next chapter.

Now it's time to mount the CRUDController:

```php
$app->mount('/crud', new CRUDlex\CRUDControllerProvider());
```

And that's it. Your CRUD UI should be available now, for example if you
defined a book entity in the crud.yml:

http://.../crud/book

---

Previous: [Introduction](1_introduction.md)

Next: [Data Structure Definition](3_datastructures.md)

[Table of Contents](0_manual.md)
