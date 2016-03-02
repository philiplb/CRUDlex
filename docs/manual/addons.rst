Addons
======

There are several surrounding projects around CRUDlex. Each of them is described
here.

Each addon has tags following the versioning of CRUDlex. So the version 0.9.9
will work with CRUDlex 0.9.9 etc.. The master branch works against the master of
CRUDlex.

----------------------------
CRUDlexAmazonS3FileProcessor
----------------------------

The `CRUDlexAmazonS3FileProcessor <https://github.com/philiplb/CRUDlexAmazonS3FileProcessor)>`_
handles the file uploads via Amazon S3.

This is how to use it:

First, create an instance of the Amazon S3 FileProcessor:

.. code-block:: php

    $fileProcessor = new CRUDlex\AmazonS3FileProcessor(
        'yourBucket',
        'yourAccessKey',
        'yourSecretAccessKey'
    );

And then hand it in when registering the CRUDlex ServiceProvider:

.. code-block:: php

    $app->register(new CRUDlex\ServiceProvider(), array(
        'crud.file' => __DIR__ . '<yourCrud.yml>',
        'crud.datafactory' => $dataFactory,
        'crud.fileprocessor' => $fileProcessor
    ));

-----------
CRUDlexUser
-----------

`CRUDlexUser <https://github.com/philiplb/CRUDlexUser)>`_ is a library offering
an user provider for symfony/security

This library offers two parts. First, a management interface for your admin panel to
perform CRUD operations on your userbase and second, an symfony/security UserProvider
offering in order to connect the users with the application.

^^^^^^^^^^^^^^^
The Admin Panel
^^^^^^^^^^^^^^^

All you have to do is to add the needed entities to your crud.yml from the
following sub chapters.

In order to get the salt generated and the password hashed, you have to let the
library add some CRUDlex events in your initialization:

.. code-block:: php

    $crudUserSetup = new CRUDlex\UserSetup();
    $crudUserSetup->addEvents($app['crud']->getData('user'));

"""""
Users
"""""

.. code-block:: yaml

    user:
        label: User
        table: user
        fields:
            username:
                type: text
                label: Username
                required: true
                unique: true
            password:
                type: text
                label: Password Hash
                description: 'Set this to your desired password. Will be automatically converted to an hash value not meant to be readable.'
                required: true
            salt:
                type: text
                label: Password Salt
                description: 'Auto populated field on user creation. Used internally.'
                required: false

Plus any more fields you need.

"""""
Roles
"""""

.. code-block:: yaml

    role:
        label: Roles
        table: role
        fields:
            role:
                type: text
                label: Role
                required: true

""""""""""""""""""""""""""
Connecting Users and Roles
""""""""""""""""""""""""""

.. code-block:: yaml

    userRole:
        label: User Roles
        table: userRole
        fields:
            user:
                type: reference
                label: User
                reference:
                    table: user
                    nameField: username
                    entity: user
                required: true
            role:
                type: reference
                label: Role
                reference:
                    table: role
                    nameField: role
                    entity: role
                required: true

^^^^^^^^^^^^^^^^
The UserProvider
^^^^^^^^^^^^^^^^

Simply instantiate and add it to your symfony/security configuration:

.. code-block:: php

    $userProvider = new CRUDlex\UserProvider($app['crud']->getData('user'), $app['crud']->getData('userRole'));
    $app->register(new Silex\Provider\SecurityServiceProvider(), array(
        'security.firewalls' => array(
            'admin' => array(
                //...
                'users' => $userProvider
            ),
        ),
    ));
