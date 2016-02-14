-----------------------------------
CRUDlex\\CRUDFileProcessorInterface
-----------------------------------

.. toctree::
   :maxdepth: 1

   CRUDSimpleFilesystemFileProcessor

.. php:namespace: CRUDlex

.. php:interface:: CRUDFileProcessorInterface

    This interface is used to handle file uploads.

    .. php:method:: createFile(Request $request, CRUDEntity $entity, $entityName, $field)

        Creates the uploaded file of a newly created entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: CRUDEntity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the file field

    .. php:method:: updateFile(Request $request, CRUDEntity $entity, $entityName, $field)

        Updates the uploaded file of an updated entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: CRUDEntity
        :param $entity: the updated entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the file field

    .. php:method:: deleteFile(CRUDEntity $entity, $entityName, $field)

        Deletes a specific file from an existing entity.

        :type $entity: CRUDEntity
        :param $entity: the entity to delete the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be deleted

    .. php:method:: renderFile(CRUDEntity $entity, $entityName, $field)

        Renders (outputs) a file of an entity. This includes setting headers
        like the file size, mimetype and name, too.

        :type $entity: CRUDEntity
        :param $entity: the entity to render the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be rendered
        :returns: \Symfony\Component\HttpFoundation\Response the HTTP response, likely to be a streamed one
