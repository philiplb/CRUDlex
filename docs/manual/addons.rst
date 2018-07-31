Addons
======

There are several surrounding projects around CRUDlex. Each of them is described here.

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

.. tabs::

   .. group-tab:: Symfony 4

      The boot method of the kernel is a possible place to add the events:

      .. code-block:: php

          public function boot() {
              parent::boot();
              $crudUserSetup = new CRUDlex\UserSetup();
              $crudUserSetup->addEvents($this->getContainer()->get('crudlex.service')->getData('user'));
          }

   .. group-tab:: Silex 2

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
            userRoles:
                type: many
                label: Roles
                many:
                    entity: role
                    nameField: role
                    thisField: user
                    thatField: role


Plus any more fields you need.

Recommended for the password reset features:

.. code-block:: yaml

    email:
        type: text
        label: E-Mail
        required: true
        unique: true

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

^^^^^^^^^^^^^^
Password Reset
^^^^^^^^^^^^^^

In case you want to use the password reset features:

.. code-block:: yaml

    passwordReset:
        label: Password Resets
        table: password_reset
        fields:
            user:
                type: reference
                label: User
                reference:
                    nameField: username
                    entity: user
                required: true
            token:
                type: text
                label: Token
                required: true
            reset:
                type: datetime
                label: Reset

^^^^^^^^^^^^^^^^
The UserProvider
^^^^^^^^^^^^^^^^

Simply add the user provider to your symfony/security configuration:

.. tabs::

   .. group-tab:: Symfony 4

      Define the service (here the most simple way):


      .. code-block:: yaml

        CRUDlex\UserProvider:
            public: true
            class: "CRUDlex\\UserProvider"
            arguments: ["@crudlex.service"]


     Then, the symfony/security user provider is set:

      .. code-block:: yaml

          security:
              providers:
                  webservice:
                      id: CRUDlex\UserProvider

   .. group-tab:: Silex 2

      .. code-block:: php

          $userProvider = new CRUDlex\UserProvider($app['crud']);
          $app->register(new Silex\Provider\SecurityServiceProvider(), [
              'security.firewalls' => [
                  'admin' => [
                      //...
                      'users' => $userProvider
                  ],
              ],
          ]);


^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Accessing Data of the Logged in User
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to get the user data from the logged in user in your controller, you
might grab him like this:

.. tabs::

   .. group-tab:: Symfony 4

      From within a controller, the user can be accessed via

      .. code-block:: php

          $user = $this->getUser();

      This is a shortcut for using the service "security.token_storage":

      .. code-block:: php

          $user = $this->container->getParameter('security.token_storage')->getToken();

   .. group-tab:: Silex 2

      .. code-block:: php

          $user = $app['security.token_storage']->getToken();

You get back a CRUDlex\\User instance having some getters, see the API docs.
