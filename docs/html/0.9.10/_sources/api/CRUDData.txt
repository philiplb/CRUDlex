-----------------
CRUDlex\\CRUDData
-----------------

.. toctree::
   :maxdepth: 1

   CRUDMySQLData

.. php:namespace: CRUDlex

.. php:class:: CRUDData

    The abstract class for reading and writing data.

    .. php:const:: DELETION_SUCCESS

        Return value on successful deletion.

    .. php:const:: DELETION_FAILED_STILL_REFERENCED

        Return value on failed deletion due to existing references.

    .. php:const:: DELETION_FAILED_EVENT

        Return value on failed deletion due to a failed before delete event.

    .. php:attr:: definition

        protected

        Holds the {@see CRUDEntityDefinition} entity definition.

    .. php:attr:: fileProcessor

        protected

        Holds the {@see CRUDFileProcessorInterface} file processor.

    .. php:attr:: events

        protected

        Holds the events.

    .. php:method:: doDelete(CRUDEntity $entity, $deleteCascade)

        Performs the actual deletion.

        :type $entity: CRUDEntity
        :param $entity: the id of the entry to delete
        :type $deleteCascade: boolean
        :param $deleteCascade: whether to delete children and subchildren
        :returns: integer true on successful deletion

    .. php:method:: hydrate($row)

        Creates an {@see CRUDEntity} from the raw data array with the field name
        as keys and field values as values.

        :type $row: array
        :param $row: the array with the raw data
        :returns: CRUDEntity the entity containing the array data then

    .. php:method:: executeEvents(CRUDEntity $entity, $moment, $action)

        Executes the event chain of an entity.

        :type $entity: CRUDEntity
        :param $entity: the entity having the event chain to execute
        :type $moment: string
        :param $moment: the "moment" of the event, can be either "before" or "after"
        :type $action: string
        :param $action: the "action" of the event, can be either "create", "update" or "delete"
        :returns: boolean true on successful execution of the full chain or false if it broke at any point (and stopped the execution)

    .. php:method:: performOnFiles(CRUDEntity $entity, $entityName, $function)

        Executes a function for each file field of this entity.

        :type $entity: CRUDEntity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $function: \Closure
        :param $function: the function to perform, takes $entity, $entityName and $field as parameter

    .. php:method:: pushEvent($moment, $action, $function)

        Adds an event to fire for the given parameters. The event function must
        have this signature:
        function (CRUDEntity $entity)
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

    .. php:method:: get($id)

        Gets the entity with the given id.

        :type $id: string
        :param $id: the id
        :returns: CRUDEntity the entity belonging to the id or null if not existant

    .. php:method:: listEntries($filter = array(), $filterOperators = array(), $skip = null, $amount = null, $sortField = null, $sortAscending = null)

        Gets a list of entities fullfilling the given filter or all if no
        selection was given.

        :type $filter: array
        :param $filter: the filter all resulting entities must fulfill, the keys as field names
        :type $filterOperators: array
        :param $filterOperators: the operators of the filter like "=" defining the full condition of the field
        :type $skip: integer
        :param $skip: if given and not null, it specifies the amount of rows to skip
        :type $amount: integer
        :param $amount: if given and not null, it specifies the maximum amount of rows to retrieve
        :type $sortField: string
        :param $sortField: if given and not null, it specifies the field to sort the entries
        :type $sortAscending: boolean
        :param $sortAscending: if given and not null, it specifies that the sort order is ascending, descending else
        :returns: CRUDEntity[] the entities fulfilling the filter or all if no filter was given

    .. php:method:: create(CRUDEntity $entity)

        Persists the given entity as new entry in the datasource.

        :type $entity: CRUDEntity
        :param $entity: the entity to persist
        :returns: boolean true on successful creation

    .. php:method:: update(CRUDEntity $entity)

        Updates an existing entry in the datasource having the same id.

        :type $entity: CRUDEntity
        :param $entity: the entity with the new data

    .. php:method:: delete($entity)

        Deletes an entry from the datasource having the given id.

        :type $entity: CRUDEntity
        :param $entity: the id of the entry to delete
        :returns: integer returns one of: - CRUDData::DELETION_SUCCESS -> successful deletion - CRUDData::DELETION_FAILED_STILL_REFERENCED -> failed deletion due to existing references - CRUDData::DELETION_FAILED_EVENT -> failed deletion due to a failed before delete event

    .. php:method:: getReferences($table, $nameField)

        Gets ids and names of a table. Used for building up the dropdown box of
        reference type fields.

        :type $table: string
        :param $table: the table
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
        :type $excludeDeleted: bool
        :param $excludeDeleted: false, if soft deleted entries in the datasource should be counted, too
        :returns: int the count fulfilling the given parameters

    .. php:method:: fetchReferences($entities = null)

        Adds the id and name of referenced entities to the given entities. Each
        reference field is before the raw id of the referenced entity and after
        the fetch, it's an array with the keys id and name.

        :param $entities:

    .. php:method:: getDefinition()

        Gets the {@see CRUDEntityDefinition} instance.

        :returns: CRUDEntityDefinition the definition instance

    .. php:method:: createEmpty()

        Creates a new, empty entity instance having all fields prefilled with
        null or the defined value in case of fixed fields.

        :returns: CRUDEntity the newly created entity

    .. php:method:: createFiles(Request $request, CRUDEntity $entity, $entityName)

        Creates the uploaded files of a newly created entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: CRUDEntity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it

    .. php:method:: updateFiles(Request $request, CRUDEntity $entity, $entityName)

        Updates the uploaded files of an updated entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: CRUDEntity
        :param $entity: the updated entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it

    .. php:method:: deleteFile(CRUDEntity $entity, $entityName, $field)

        Deletes a specific file from an existing entity.

        :type $entity: CRUDEntity
        :param $entity: the entity to delete the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be deleted

    .. php:method:: deleteFiles(CRUDEntity $entity, $entityName)

        Deletes all files of an existing entity.

        :type $entity: CRUDEntity
        :param $entity: the entity to delete the files from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it

    .. php:method:: renderFile(CRUDEntity $entity, $entityName, $field)

        Renders (outputs) a file of an entity. This includes setting headers
        like the file size, mimetype and name, too.

        :type $entity: CRUDEntity
        :param $entity: the entity to render the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be rendered
        :returns: Response the HTTP response, likely to be a streamed one
