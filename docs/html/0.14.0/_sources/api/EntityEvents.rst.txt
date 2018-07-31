---------------------
CRUDlex\\EntityEvents
---------------------

.. php:namespace: CRUDlex

.. php:class:: EntityEvents

    Management and execution of events.

    .. php:attr:: events

        protected array

        Holds the events.

    .. php:method:: shouldExecute(Entity $entity, $moment, $action)

        Executes the event chain of an entity.

        :type $entity: Entity
        :param $entity: the entity having the event chain to execute
        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: boolean true on successful execution of the full chain or false if it broke at any point (and stopped the execution)

    .. php:method:: push($moment, $action, Closure $function)

        Adds an event to fire for the given parameters. The event function must
        have this signature:
        function (Entity $entity)
        and has to return true or false.
        The events are executed one after another in the added order as long as
        they return "true". The first event returning "false" will stop the
        process.

        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :type $function: Closure
        :param $function: the event function to be called if set

    .. php:method:: pop($moment, $action)

        Removes and returns the latest event for the given parameters.

        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: \Closure|null the popped event or null if no event was available.
