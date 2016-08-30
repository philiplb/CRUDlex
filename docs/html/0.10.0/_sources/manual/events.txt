Events
======

There are situations where you might want to react before or after an entity is
modified. For example hashing the password of an user object instead of storing
it directly in clear text in the database. Or you want to get an E-Mail every
time someone deletes a library object.

This is what events are for. You can define closures which are executed in
certain moments and can even interrupt the modification of the data.

This is how you define an event which is executed before an entity is created:

.. code-block:: php

    $app['crud']->getData('library')->pushEvent('before', 'create', function(CRUDlex\Entity $entity) {
        // Do something with the entity which is about to be saved.
        return true;
    });

This code should go in your setup directly after the ServiceProvider is
registered.

*pushEvent* takes three parameters:

* The moment of the event, can be:

  * before
  * after

* The action of the event, can be:

  * create: On entity creation
  * update: On entity update
  * delete: On entity deletion
  * createFiles: On created entity files handling
  * updateFiles: On updated entity files handling
  * deleteFiles: On deleted entity files handling
  * deleteFile: On deleting a file of an entity

* The closure to execute on this event. Signature: **function(CRUDlex\\Entity $entity)**

You can push as many events for a moment and an action as you like. They will
be executed in the order they were added.

The before events must return a boolean. The first event returning false is
canceling the whole action and so the entity doesn't get created, updated or
deleted.

With *popEvent*, the last added event of the given moment and action is
removed from the list and the closure is returned:

.. code-block:: php

    $closure = $app['crud']->getData('library')->popEvent('before', 'create');

If no more events are available, *popEvent* will return *null*.
