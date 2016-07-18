-------------------------------
CRUDlex\\FileProcessorInterface
-------------------------------

.. toctree::
   :maxdepth: 1

   SimpleFilesystemFileProcessor

.. php:namespace: CRUDlex

.. php:interface:: FileProcessorInterface

    This interface is used to handle file uploads.

    .. php:method:: createFile(Request $request, Entity $entity, $entityName, $field)

        Creates the uploaded file of a newly created entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the just created entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the file field
        :returns: void

    .. php:method:: updateFile(Request $request, Entity $entity, $entityName, $field)

        Updates the uploaded file of an updated entity.

        :type $request: Request
        :param $request: the HTTP request containing the file data
        :type $entity: Entity
        :param $entity: the updated entity
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the file field
        :returns: void

    .. php:method:: deleteFile(Entity $entity, $entityName, $field)

        Deletes a specific file from an existing entity.

        :type $entity: Entity
        :param $entity: the entity to delete the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be deleted
        :returns: void

    .. php:method:: renderFile(Entity $entity, $entityName, $field)

        Renders (outputs) a file of an entity. This includes setting headers
        like the file size, mimetype and name, too.

        :type $entity: Entity
        :param $entity: the entity to render the file from
        :type $entityName: string
        :param $entityName: the name of the entity as this class here is not aware of it
        :type $field: string
        :param $field: the field of the entity containing the file to be rendered
        :returns: \Symfony\Component\HttpFoundation\Response the HTTP response, likely to be a streamed one
