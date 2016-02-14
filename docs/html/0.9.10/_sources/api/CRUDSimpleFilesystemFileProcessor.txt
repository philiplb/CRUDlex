------------------------------------------
CRUDlex\\CRUDSimpleFilesystemFileProcessor
------------------------------------------

.. php:namespace: CRUDlex

.. php:class:: CRUDSimpleFilesystemFileProcessor

    An implementation of the {@see CRUDFileProcessorInterface} simply using the
    file system.

    .. php:method:: getPath($entityName, CRUDEntity $entity, $field)

        Constructs a file system path for the given parameters for storing the
        file of the file field.

        :type $entityName: string
        :param $entityName: the entity name
        :type $entity: CRUDEntity
        :param $entity: the entity
        :type $field: string
        :param $field: the file field in the entity
        :returns: string the constructed path for storing the file of the file field

    .. php:method:: createFile(Request $request, CRUDEntity $entity, $entityName, $field)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :type $entity: CRUDEntity
        :param $entity:
        :param $entityName:
        :param $field:

    .. php:method:: updateFile(Request $request, CRUDEntity $entity, $entityName, $field)

        {@inheritdoc}
        For now, this implementation is defensive and doesn't delete ever.

        :type $request: Request
        :param $request:
        :type $entity: CRUDEntity
        :param $entity:
        :param $entityName:
        :param $field:

    .. php:method:: deleteFile(CRUDEntity $entity, $entityName, $field)

        {@inheritdoc}
        For now, this implementation is defensive and doesn't delete ever.

        :type $entity: CRUDEntity
        :param $entity:
        :param $entityName:
        :param $field:

    .. php:method:: renderFile(CRUDEntity $entity, $entityName, $field)

        {@inheritdoc}

        :type $entity: CRUDEntity
        :param $entity:
        :param $entityName:
        :param $field:
