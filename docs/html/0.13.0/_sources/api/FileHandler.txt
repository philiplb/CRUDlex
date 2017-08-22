--------------------
CRUDlex\\FileHandler
--------------------

.. php:namespace: CRUDlex

.. php:class:: FileHandler

    Handles the files.

    .. php:attr:: filesystem

        protected FilesystemInterface

        Brings the abstract access to the filesystem.

    .. php:attr:: entityDefinition

        protected EntityDefinition

        Holds the entity definition.

    .. php:method:: getPath($entityName, Entity $entity, $field)

        Constructs a file system path for the given parameters for storing the
        file of the file field.

        :type $entityName: string
        :param $entityName: the entity name
        :type $entity: Entity
        :param $entity: the entity
        :type $field: string
        :param $field: the file field in the entity
        :returns: string the constructed path for storing the file of the file field

    .. php:method:: performOnFiles(Entity $entity, $entityName, $function)

        Executes a function for each file field of this entity.

        :type $entity: Entity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $function: \Closure
        :param $function: the function to perform, takes $entity, $entityName and $field as parameter

    .. php:method:: shouldWriteFile(AbstractData $data, Request $request, Entity $entity, $entityName, $action)

        Writes the uploaded files.

        :type $data: AbstractData
        :param $data: the AbstractData instance who should receive the events
        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the just manipulated entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $action: string
        :param $action: the name of the performed action
        :returns: boolean true if all before events passed

    .. php:method:: __construct(FilesystemInterface $filesystem, EntityDefinition $entityDefinition)

        FileHandler constructor.

        :type $filesystem: FilesystemInterface
        :param $filesystem: the filesystem to use
        :type $entityDefinition: EntityDefinition
        :param $entityDefinition:

    .. php:method:: renderFile(Entity $entity, $entityName, $field)

        Renders (outputs) a file of an entity. This includes setting headers
        like the file size, mimetype and name, too.

        :type $entity: Entity
        :param $entity: the entity to render the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be rendered
        :returns: StreamedResponse the HTTP streamed response

    .. php:method:: deleteFiles(AbstractData $data, Entity $entity, $entityName)

        Deletes all files of an existing entity.

        :type $data: AbstractData
        :param $data: the AbstractData instance who should receive the events
        :type $entity: Entity
        :param $entity: the entity to delete the files from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :returns: boolean true on successful deletion

    .. php:method:: deleteFile(AbstractData $data, Entity $entity, $entityName, $field)

        Deletes a specific file from an existing entity.

        :type $data: AbstractData
        :param $data: the AbstractData instance who should receive the events
        :type $entity: Entity
        :param $entity: the entity to delete the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be deleted
        :returns: bool true on successful deletion true on successful deletion

    .. php:method:: createFiles(AbstractData $data, Request $request, Entity $entity, $entityName)

        Creates the uploaded files of a newly created entity.

        :type $data: AbstractData
        :param $data: the AbstractData instance who should receive the events
        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :returns: boolean true if all before events passed

    .. php:method:: updateFiles(AbstractData $data, Request $request, Entity $entity, $entityName)

        Updates the uploaded files of an updated entity.

        :type $data: AbstractData
        :param $data: the AbstractData instance who should receive the events
        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the updated entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :returns: boolean true on successful update
