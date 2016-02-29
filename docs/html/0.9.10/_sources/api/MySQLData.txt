------------------
CRUDlex\\MySQLData
------------------

.. php:namespace: CRUDlex

.. php:class:: MySQLData

    MySQL Data implementation using a given Doctrine DBAL instance.

    .. php:const:: DELETION_SUCCESS

        Return value on successful deletion.

    .. php:const:: DELETION_FAILED_STILL_REFERENCED

        Return value on failed deletion due to existing references.

    .. php:const:: DELETION_FAILED_EVENT

        Return value on failed deletion due to a failed before delete event.

    .. php:attr:: db

        protected

        Holds the Doctrine DBAL instance.

    .. php:attr:: useUUIDs

        protected

        Flag whether to use UUIDs as primary key.

    .. php:attr:: definition

        protected

        Holds the {@see EntityDefinition} entity definition.

    .. php:attr:: fileProcessor

        protected

        Holds the {@see FileProcessorInterface} file processor.

    .. php:attr:: events

        protected

        Holds the events.

    .. php:method:: setValuesAndParameters(Entity $entity, QueryBuilder $queryBuilder, $setValue)

        Sets the values and parameters of the upcoming given query according
        to the entity.

        :type $entity: Entity
        :param $entity: the entity with its fields and values
        :type $queryBuilder: QueryBuilder
        :param $queryBuilder: the upcoming query
        :type $setValue: boolean
        :param $setValue: whether to use QueryBuilder::setValue (true) or QueryBuilder::set (false)

    .. php:method:: deleteChildren($id, $deleteCascade)

        Performs the cascading children deletion.

        :type $id: integer
        :param $id: the current entities id
        :type $deleteCascade: boolean
        :param $deleteCascade: whether to delete children and subchildren

    .. php:method:: hasChildren($id)

        Checks whether the by id given entity still has children referencing it.

        :type $id: integer
        :param $id: the current entities id
        :returns: boolean true if the entity still has children

    .. php:method:: doDelete(Entity $entity, $deleteCascade)

        {@inheritdoc}

        :type $entity: Entity
        :param $entity:
        :param $deleteCascade:

    .. php:method:: addFilter(QueryBuilder $queryBuilder, $filter, $filterOperators)

        Adds sorting parameters to the query.

        :type $queryBuilder: QueryBuilder
        :param $queryBuilder: the query
        :param $filter:
        :param $filterOperators:

    .. php:method:: addPagination(QueryBuilder $queryBuilder, $skip, $amount)

        Adds pagination parameters to the query.

        :type $queryBuilder: QueryBuilder
        :param $queryBuilder: the query
        :type $skip: integer|null
        :param $skip: the rows to skip
        :type $amount: integer|null
        :param $amount: the maximum amount of rows

    .. php:method:: addSort(QueryBuilder $queryBuilder, $sortField, $sortAscending)

        Adds sorting parameters to the query.

        :type $queryBuilder: QueryBuilder
        :param $queryBuilder: the query
        :type $sortField: string|null
        :param $sortField: the sort field
        :type $sortAscending: boolean|null
        :param $sortAscending: true if sort ascending, false if descending

    .. php:method:: fetchReferencesForField($entities, $field)

        Adds the id and name of referenced entities to the given entities. The
        reference field is before the raw id of the referenced entity and after
        the fetch, it's an array with the keys id and name.

        :param $entities:
        :type $field: string
        :param $field: the reference field

    .. php:method:: generateUUID()

        Genereates a new UUID.

        :returns: string|null the new UUID or null if this instance isn't configured to do so

    .. php:method:: __construct(EntityDefinition $definition, FileProcessorInterface $fileProcessor, $db, $useUUIDs)

        Constructor.

        :type $definition: EntityDefinition
        :param $definition: the entity definition
        :type $fileProcessor: FileProcessorInterface
        :param $fileProcessor: the file processor to use
        :param $db:
        :type $useUUIDs: boolean
        :param $useUUIDs: flag whether to use UUIDs as primary key

    .. php:method:: get($id)

        {@inheritdoc}

        :param $id:

    .. php:method:: listEntries($filter = array(), $filterOperators = array(), $skip = null, $amount = null, $sortField = null, $sortAscending = null)

        {@inheritdoc}

        :param $filter:
        :param $filterOperators:
        :param $skip:
        :param $amount:
        :param $sortField:
        :param $sortAscending:

    .. php:method:: create(Entity $entity)

        {@inheritdoc}

        :type $entity: Entity
        :param $entity:

    .. php:method:: update(Entity $entity)

        {@inheritdoc}

        :type $entity: Entity
        :param $entity:

    .. php:method:: getReferences($table, $nameField)

        {@inheritdoc}

        :param $table:
        :param $nameField:

    .. php:method:: countBy($table, $params, $paramsOperators, $excludeDeleted)

        {@inheritdoc}

        :param $table:
        :param $params:
        :param $paramsOperators:
        :param $excludeDeleted:

    .. php:method:: fetchReferences($entities = null)

        {@inheritdoc}

        :param $entities:

    .. php:method:: hydrate($row)

        Creates an {@see Entity} from the raw data array with the field name
        as keys and field values as values.

        :type $row: array
        :param $row: the array with the raw data
        :returns: Entity the entity containing the array data then

    .. php:method:: executeEvents(Entity $entity, $moment, $action)

        Executes the event chain of an entity.

        :type $entity: Entity
        :param $entity: the entity having the event chain to execute
        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: boolean true on successful execution of the full chain or false if it broke at any point (and stopped the execution)

    .. php:method:: performOnFiles(Entity $entity, $entityName, $function)

        Executes a function for each file field of this entity.

        :type $entity: Entity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $function: \Closure
        :param $function: the function to perform, takes $entity, $entityName and $field as parameter

    .. php:method:: pushEvent($moment, $action, $function)

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
        :type $function: anonymous
        :param $function: $function the event function to be called if set

    .. php:method:: popEvent($moment, $action)

        Removes and returns the latest event for the given parameters.

        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: anonymous function the popped event or null if no event was available.

    .. php:method:: delete($entity)

        Deletes an entry from the datasource having the given id.

        :type $entity: Entity
        :param $entity: the id of the entry to delete
        :returns: integer returns one of: - Data::DELETION_SUCCESS -> successful deletion - Data::DELETION_FAILED_STILL_REFERENCED -> failed deletion due to existing references - Data::DELETION_FAILED_EVENT -> failed deletion due to a failed before delete event

    .. php:method:: getDefinition()

        Gets the {@see EntityDefinition} instance.

        :returns: EntityDefinition the definition instance

    .. php:method:: createEmpty()

        Creates a new, empty entity instance having all fields prefilled with
        null or the defined value in case of fixed fields.

        :returns: Entity the newly created entity

    .. php:method:: createFiles(Request $request, Entity $entity, $entityName)

        Creates the uploaded files of a newly created entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it

    .. php:method:: updateFiles(Request $request, Entity $entity, $entityName)

        Updates the uploaded files of an updated entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the updated entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it

    .. php:method:: deleteFile(Entity $entity, $entityName, $field)

        Deletes a specific file from an existing entity.

        :type $entity: Entity
        :param $entity: the entity to delete the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be deleted

    .. php:method:: deleteFiles(Entity $entity, $entityName)

        Deletes all files of an existing entity.

        :type $entity: Entity
        :param $entity: the entity to delete the files from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it

    .. php:method:: renderFile(Entity $entity, $entityName, $field)

        Renders (outputs) a file of an entity. This includes setting headers
        like the file size, mimetype and name, too.

        :type $entity: Entity
        :param $entity: the entity to render the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be rendered
        :returns: Response the HTTP response, likely to be a streamed one
