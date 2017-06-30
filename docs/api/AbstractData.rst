---------------------
CRUDlex\\AbstractData
---------------------

.. toctree::
  :maxdepth: 1

  MySQLData

.. php:namespace: CRUDlex

.. php:class:: AbstractData

    The abstract class for reading and writing data.

    .. php:const:: DELETION_SUCCESS

        Return value on successful deletion.

    .. php:const:: DELETION_FAILED_STILL_REFERENCED

        Return value on failed deletion due to existing references.

    .. php:const:: DELETION_FAILED_EVENT

        Return value on failed deletion due to a failed before delete event.

    .. php:attr:: definition

        protected EntityDefinition

        Holds the entity definition.

    .. php:attr:: filesystem

        protected FilesystemInterface

        Holds the filesystem.

    .. php:attr:: events

        protected array

        Holds the events.

    .. php:method:: doDelete(Entity $entity, $deleteCascade)

        Performs the actual deletion.

        :type $entity: Entity
        :param $entity: the id of the entry to delete
        :type $deleteCascade: boolean
        :param $deleteCascade: whether to delete children and subchildren
        :returns: integer true on successful deletion

    .. php:method:: hydrate($row)

        Creates an Entity from the raw data array with the field name
        as keys and field values as values.

        :type $row: array
        :param $row: the array with the raw data
        :returns: Entity the entity containing the array data then

    .. php:method:: enrichEntityWithMetaData($id, Entity $entity)

        Enriches an entity with metadata:
        id, version, created_at, updated_at

        :type $id: mixed
        :param $id: the id of the entity to enrich
        :type $entity: Entity
        :param $entity: the entity to enrich

    .. php:method:: getManyFields()

        Gets the many-to-many fields.

        :returns: array|\string[] the many-to-many fields

    .. php:method:: getFormFields()

        Gets all form fields including the many-to-many-ones.

        :returns: array all form fields

    .. php:method:: deleteChildren($id, $deleteCascade)

        Performs the cascading children deletion.

        :type $id: integer
        :param $id: the current entities id
        :type $deleteCascade: boolean
        :param $deleteCascade: whether to delete children and sub children
        :returns: integer returns one of: - AbstractData::DELETION_SUCCESS -> successful deletion - AbstractData::DELETION_FAILED_STILL_REFERENCED -> failed deletion due to existing references - AbstractData::DELETION_FAILED_EVENT -> failed deletion due to a failed before delete event

    .. php:method:: getReferenceIds($entities, $field)

        Gets an array of reference ids for the given entities.

        :type $entities: array
        :param $entities: the entities to extract the ids
        :type $field: string
        :param $field: the reference field
        :returns: array the extracted ids

    .. php:method:: doCreate(Entity $entity)

        Performs the persistence of the given entity as new entry in the
        datasource.

        :type $entity: Entity
        :param $entity: the entity to persist
        :returns: boolean true on successful creation

    .. php:method:: doUpdate(Entity $entity)

        Performs the updates of an existing entry in the datasource having the
        same id.

        :type $entity: Entity
        :param $entity: the entity with the new data
        :returns: boolean true on successful update

    .. php:method:: shouldExecuteEvents(Entity $entity, $moment, $action)

        Executes the event chain of an entity.

        :type $entity: Entity
        :param $entity: the entity having the event chain to execute
        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: boolean true on successful execution of the full chain or false if it broke at any point (and stopped the execution)

    .. php:method:: pushEvent($moment, $action, Closure $function)

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

    .. php:method:: popEvent($moment, $action)

        Removes and returns the latest event for the given parameters.

        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: \Closure|null the popped event or null if no event was available.

    .. php:method:: get($id)

        Gets the entity with the given id.

        :type $id: string
        :param $id: the id
        :returns: Entity the entity belonging to the id or null if not existant

    .. php:method:: listEntries($filter = [], $filterOperators = [], $skip = null, $amount = null, $sortField = null, $sortAscending = null)

        Gets a list of entities fullfilling the given filter or all if no
        selection was given.

        :type $filter: array
        :param $filter: the filter all resulting entities must fulfill, the keys as field names
        :type $filterOperators: array
        :param $filterOperators: the operators of the filter like "=" defining the full condition of the field
        :type $skip: integer|null
        :param $skip: if given and not null, it specifies the amount of rows to skip
        :type $amount: integer|null
        :param $amount: if given and not null, it specifies the maximum amount of rows to retrieve
        :type $sortField: string|null
        :param $sortField: if given and not null, it specifies the field to sort the entries
        :type $sortAscending: boolean|null
        :param $sortAscending: if given and not null, it specifies that the sort order is ascending, descending else
        :returns: Entity[] the entities fulfilling the filter or all if no filter was given

    .. php:method:: create(Entity $entity)

        Persists the given entity as new entry in the datasource.

        :type $entity: Entity
        :param $entity: the entity to persist
        :returns: boolean true on successful creation

    .. php:method:: update(Entity $entity)

        Updates an existing entry in the datasource having the same id.

        :type $entity: Entity
        :param $entity: the entity with the new data
        :returns: boolean true on successful update

    .. php:method:: delete($entity)

        Deletes an entry from the datasource.

        :type $entity: Entity
        :param $entity: the entity to delete
        :returns: integer returns one of: - AbstractData::DELETION_SUCCESS -> successful deletion - AbstractData::DELETION_FAILED_STILL_REFERENCED -> failed deletion due to existing references - AbstractData::DELETION_FAILED_EVENT -> failed deletion due to a failed before delete event

    .. php:method:: getIdToNameMap($entity, $nameField)

        Gets ids and names of a table. Used for building up the dropdown box of
        reference type fields for example.

        :type $entity: string
        :param $entity: the entity
        :type $nameField: string
        :param $nameField: the field defining the name of the rows
        :returns: array an array with the ids as key and the names as values

    .. php:method:: countBy($table, $params, $paramsOperators, $excludeDeleted)

        Retrieves the amount of entities in the datasource fulfilling the given
        parameters.

        :type $table: string
        :param $table: the table to count in
        :type $params: array
        :param $params: an array with the field names as keys and field values as values
        :type $paramsOperators: array
        :param $paramsOperators: the operators of the parameters like "=" defining the full condition of the field
        :type $excludeDeleted: boolean
        :param $excludeDeleted: false, if soft deleted entries in the datasource should be counted, too
        :returns: integer the count fulfilling the given parameters

    .. php:method:: hasManySet($field, $thatIds, $excludeId = null)

        Checks whether a given set of ids is assigned to any entity exactly
        like it is given (no subset, no superset).

        :type $field: string
        :param $field: the many field
        :type $thatIds: array
        :param $thatIds: the id set to check
        :type $excludeId: string|null
        :param $excludeId: one optional own id to exclude from the check
        :returns: boolean true if the set of ids exists for an entity

    .. php:method:: getDefinition()

        Gets the EntityDefinition instance.

        :returns: EntityDefinition the definition instance

    .. php:method:: createEmpty()

        Creates a new, empty entity instance having all fields prefilled with
        null or the defined value in case of fixed fields.

        :returns: Entity the newly created entity
