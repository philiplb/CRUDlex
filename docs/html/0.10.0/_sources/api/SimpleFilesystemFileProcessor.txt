--------------------------------------
CRUDlex\\SimpleFilesystemFileProcessor
--------------------------------------

.. php:namespace: CRUDlex

.. php:class:: SimpleFilesystemFileProcessor

    An implementation of the {@see FileProcessorInterface} simply using the
    file system.

    .. php:attr:: basePath

        protected

        Holds the base path where all files will be stored into subfolders.

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

    .. php:method:: __construct($basePath = '')

        Constructor.

        :type $basePath: string
        :param $basePath: the base path where all files will be stored into subfolders

    .. php:method:: createFile(Request $request, Entity $entity, $entityName, $field)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :type $entity: Entity
        :param $entity:
        :param $entityName:
        :param $field:

    .. php:method:: updateFile(Request $request, Entity $entity, $entityName, $field)

        {@inheritdoc}
        For now, this implementation is defensive and doesn't delete ever.

        :type $request: Request
        :param $request:
        :type $entity: Entity
        :param $entity:
        :param $entityName:
        :param $field:

    .. php:method:: deleteFile(Entity $entity, $entityName, $field)

        {@inheritdoc}
        For now, this implementation is defensive and doesn't delete ever.

        :type $entity: Entity
        :param $entity:
        :param $entityName:
        :param $field:

    .. php:method:: renderFile(Entity $entity, $entityName, $field)

        {@inheritdoc}

        :type $entity: Entity
        :param $entity:
        :param $entityName:
        :param $field:
