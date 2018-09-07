----------------------------
CRUDlex\\ControllerInterface
----------------------------

.. toctree::
  :maxdepth: 1

  Controller

.. php:namespace: CRUDlex

.. php:interface:: ControllerInterface

    This represents the Controller offering all CRUD pages.

    It offers functions for this routes:

    "/resource/static" serving static resources

    "/{entity}/create" creation page of the entity

    "/{entity}" list page of the entity

    "/{entity}/{id}" details page of a single entity instance

    "/{entity}/{id}/edit" edit page of a single entity instance

    "/{entity}/{id}/delete" POST only deletion route for an entity instance

    "/{entity}/{id}/{field}/file" renders a file field of an entity instance

    "/{entity}/{id}/{field}/delete" POST only deletion of a file field of an entity instance

    .. php:method:: setLocaleAndCheckEntity(Request $request)

        Transfers the locale from the translator to CRUDlex and

        :type $request: Request
        :param $request: the current request
        :returns: Response|null null if everything is ok, a 404 response else

    .. php:method:: create(Request $request, $entity)

        The controller for the "create" action.

        :type $request: Request
        :param $request: the current request
        :type $entity: string
        :param $entity: the current entity
        :returns: Response the HTTP response of this action

    .. php:method:: showList(Request $request, $entity)

        The controller for the "show list" action.

        :type $request: Request
        :param $request: the current request
        :type $entity: string
        :param $entity: the current entity
        :returns: Response the HTTP response of this action or 404 on invalid input

    .. php:method:: show($entity, $id)

        The controller for the "show" action.

        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id to show
        :returns: Response the HTTP response of this action or 404 on invalid input

    .. php:method:: edit(Request $request, $entity, $id)

        The controller for the "edit" action.

        :type $request: Request
        :param $request: the current request
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id to edit
        :returns: Response the HTTP response of this action or 404 on invalid input

    .. php:method:: delete(Request $request, $entity, $id)

        The controller for the "delete" action.

        :type $request: Request
        :param $request: the current request
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id to delete
        :returns: Response redirects to the entity list page or 404 on invalid input

    .. php:method:: renderFile($entity, $id, $field)

        The controller for the "render file" action.

        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id
        :type $field: string
        :param $field: the field of the file to render of the instance
        :returns: Response the rendered file

    .. php:method:: deleteFile($entity, $id, $field)

        The controller for the "delete file" action.

        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id
        :type $field: string
        :param $field: the field of the file to delete of the instance
        :returns: Response redirects to the instance details page or 404 on invalid input

    .. php:method:: staticFile(Request $request)

        The controller for serving static files.

        :type $request: Request
        :param $request: the current request
        :returns: Response redirects to the instance details page or 404 on invalid input

    .. php:method:: setLocale(Request $request, $locale)

        The controller for setting the locale.

        :type $request: Request
        :param $request: the current request
        :type $locale: string
        :param $locale: the new locale
        :returns: Response redirects to the instance details page or 404 on invalid input
