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

    .. php:attr:: database

        protected

        Holds the Doctrine DBAL instance.

    .. php:attr:: useUUIDs

        protected

        Flag whether to use UUIDs as primary key.

    .. php:attr:: definition

        protected

        Holds the {@see EntityDefinition} entity definition.

    .. php:attr:: filesystem

        protected FilesystemInterface

        Holds the filesystem.

    .. php:attr:: events

        protected

        Holds the events.

    .. php:method:: setValuesAndParameters(Entity $entity, QueryBuilder $queryBuilder, $setMethod)

        Sets the values and parameters of the upcoming given query according
        to the entity.

        :type $entity: Entity
        :param $entity: the entity with its fields and values
        :type $queryBuilder: QueryBuilder
        :param $queryBuilder: the upcoming query
        :type $setMethod: string
        :param $setMethod: what method to use on the QueryBuilder: 'setValue' or 'set'

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

    .. php:method:: getManyIds($fields, $params)

        Gets all possible many-to-many ids existing for this definition.

        :type $fields: array
        :param $fields: the many field names to fetch for
        :param $params:
        :returns: array an array of this many-to-many ids

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

        Generates a new UUID.

        :returns: string|null the new UUID or null if this instance isn't configured to do so

    .. php:method:: enrichWithManyField($idToData, $manyField)

        Enriches the given mapping of entity id to raw entity data with some
        many-to-many data.

        :type $idToData: array
        :param $idToData: a reference to the map entity id to raw entity data
        :param $manyField:

    .. php:method:: enrichWithMany($rows)

        Fetches to the rows belonging many-to-many entries and adds them to the
        rows.

        :type $rows: array
        :param $rows: the rows to enrich
        :returns: array the enriched rows

    .. php:method:: saveMany(Entity $entity)

        First, deletes all to the given entity related many-to-many entries from
        the DB
        and then writes them again.

        :type $entity: Entity
        :param $entity: the entity to save the many-to-many entries of

    .. php:method:: enrichWithReference($entities)

        Adds the id and name of referenced entities to the given entities. Each
        reference field is before the raw id of the referenced entity and after
        the fetch, it's an array with the keys id and name.

        :param $entities:
        :returns: void

    .. php:method:: doCreate(Entity $entity)

        {@inheritdoc}

        :type $entity: Entity
        :param $entity:

    .. php:method:: doUpdate(Entity $entity)

        {@inheritdoc}

        :type $entity: Entity
        :param $entity:

    .. php:method:: __construct(EntityDefinition $definition, FilesystemInterface $filesystem, $database, $useUUIDs)

        Constructor.

        :type $definition: EntityDefinition
        :param $definition: the entity definition
        :type $filesystem: FilesystemInterface
        :param $filesystem: the filesystem to use
        :param $database:
        :type $useUUIDs: boolean
        :param $useUUIDs: flag whether to use UUIDs as primary key

    .. php:method:: get($id)

        {@inheritdoc}

        :param $id:

    .. php:method:: listEntries($filter = [], $filterOperators = [], $skip = null, $amount = null, $sortField = null, $sortAscending = null)

        {@inheritdoc}

        :param $filter:
        :param $filterOperators:
        :param $skip:
        :param $amount:
        :param $sortField:
        :param $sortAscending:

    .. php:method:: getIdToNameMap($entity, $nameField)

        {@inheritdoc}

        :param $entity:
        :param $nameField:

    .. php:method:: countBy($table, $params, $paramsOperators, $excludeDeleted)

        {@inheritdoc}

        :param $table:
        :param $params:
        :param $paramsOperators:
        :param $excludeDeleted:

    .. php:method:: hasManySet($field, $thatIds, $excludeId = null)

        {@inheritdoc}

        :param $field:
        :param $thatIds:
        :param $excludeId:

    .. php:method:: hydrate($row)

        Creates an {@see Entity} from the raw data array with the field name
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

    .. php:method:: getDefinition()

        Gets the {@see EntityDefinition} instance.

        :returns: EntityDefinition the definition instance

    .. php:method:: createEmpty()

        Creates a new, empty entity instance having all fields prefilled with
        null or the defined value in case of fixed fields.

        :returns: Entity the newly created entity
