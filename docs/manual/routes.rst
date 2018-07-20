Routes
======

There are a bunch of routes used by CRUDlex which are useful to know if one wants to link to them or add middleware.

----
List
----

This are the name of the routes added by the ControllerProvider.

* **crudStatic:** fetches static resources like CSS files (GET)
* **crudCreate:** shows the new entity form (GET) and creates it (POST)
* **crudList:** shows the list of entities (GET)
* **crudShow:** shows a specific entity (GET)
* **crudEdit:** shows the update entity form (GET) and updates it (POST)
* **crudDelete:** deletes an entity (POST)
* **crudRenderFile:** shows a file of an entity (GET)
* **crudDeleteFile:** deletes a file of an entity (POST)
* **crudSetLocale:** switches the locale of the UI (GET)

-----------------
Adding Middleware
-----------------


Here is an example of how to add a middleware after mounting the ControllerProvider:

.. tabs::

   .. group-tab:: Symfony 4

      Todo

   .. group-tab:: Silex 2

      .. code-block:: php

          $app->flush();
          $route = $app['routes']->get('crudList');
          $route->before(function(Symfony\Component\HttpFoundation\Request $request) use ($app) {
              // Do your stuff.
          });
