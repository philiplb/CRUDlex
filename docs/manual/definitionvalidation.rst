Definition Validation
=====================

By default, CRUDlex validates the yml file and throws an exception if anything is wrong.

----------------------
Turning off Validation
----------------------

This costs a bit of performance, so you might want to turn it off in your production environment as it should be sure at
this point that the file is valid:

.. tabs::

   .. group-tab:: Symfony 4

      Todo

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud.validateentitydefinition'] = $app['debug'];

-------------------------------
Implementing a Custom Validator
-------------------------------

It is possible to use your own validator by implementing the interface **CRUDlex\EntityDefinitionValidatorInterface**
and handing it in before registering the service provider:

.. tabs::

   .. group-tab:: Symfony 4

      Todo

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud.entitydefinitionvalidator'] = $myCustomValidator;
