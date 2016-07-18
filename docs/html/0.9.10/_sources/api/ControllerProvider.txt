---------------------------
CRUDlex\\ControllerProvider
---------------------------

.. php:namespace: CRUDlex

.. php:class:: ControllerProvider

    This is the ControllerProvider offering all CRUD pages.

    It offers this routes:

    "/resource/static" serving static resources

    "/{entity}/create" creation page of the entity

    "/{entity}" list page of the entity

    "/{entity}/{id}" details page of a single entity instance

    "/{entity}/{id}/edit" edit page of a single entity instance

    "/{entity}/{id}/delete" POST only deletion route for an entity instance

    "/{entity}/{id}/{field}/file" renders a file field of an entity instance

    "/{entity}/{id}/{field}/delete" POST only deletion of a file field of an entity instance

    .. php:method:: getNotFoundPage(Application $app, $error)

        Generates the not found page.

        :type $app: Application
        :param $app: the Silex application
        :type $error: string
        :param $error: the cause of the not found error
        :returns: Response the rendered not found page with the status code 404

    .. php:method:: modifyFilesAndSetFlashBag(Application $app, AbstractData $crudData, Entity $instance, $entity, $mode)

        Postprocesses the entity after modification by handling the uploaded
        files and setting the flash.

        :type $app: Application
        :param $app: the current application
        :type $crudData: AbstractData
        :param $crudData: the data instance of the entity
        :type $instance: Entity
        :param $instance: the entity
        :type $entity: string
        :param $entity: the name of the entity
        :type $mode: string
        :param $mode: whether to 'edit' or to 'create' the entity
        :returns: null|\Symfony\Component\HttpFoundation\RedirectResponse the HTTP response of this modification

    .. php:method:: setValidationFailedFlashes(Application $app, $optimisticLocking, $mode)

        Sets the flashes of a failed entity modification.

        :type $app: Application
        :param $app: the current application
        :type $optimisticLocking: boolean
        :param $optimisticLocking: whether the optimistic locking failed
        :type $mode: string
        :param $mode: the modification mode, either 'create' or 'edit'

    .. php:method:: modifyEntity(Application $app, AbstractData $crudData, Entity $instance, $entity, $edit)

        Validates and saves the new or updated entity and returns the appropriate
        HTTP
        response.

        :type $app: Application
        :param $app: the current application
        :type $crudData: AbstractData
        :param $crudData: the data instance of the entity
        :type $instance: Entity
        :param $instance: the entity
        :type $entity: string
        :param $entity: the name of the entity
        :type $edit: boolean
        :param $edit: whether to edit (true) or to create (false) the entity
        :returns: Response the HTTP response of this modification

    .. php:method:: getAfterDeleteRedirectParameters(Application $app, $entity, $redirectPage)

        Gets the parameters for the redirection after deleting an entity.

        :type $app: Application
        :param $app: the current application
        :type $entity: string
        :param $entity: the entity name
        :type $redirectPage: string
        :param $redirectPage: reference, where the page to redirect to will be stored
        :returns: array<string,string> the parameters of the redirection, entity and id

    .. php:method:: buildUpListFilter(Application $app, EntityDefinition $definition, $filter, $filterActive, $filterToUse, $filterOperators)

        Builds up the parameters of the list page filters.

        :type $app: Application
        :param $app: the current application
        :type $definition: EntityDefinition
        :param $definition: the current entity definition
        :param $filter:
        :type $filterActive: boolean
        :param $filterActive: reference, will be true if at least one filter is active
        :type $filterToUse: array
        :param $filterToUse: reference, will hold a map of fields to integers (0 or 1) which boolean filters are active
        :type $filterOperators: array
        :param $filterOperators: reference, will hold a map of fields to operators for AbstractData::listEntries()

    .. php:method:: setupTemplates(Application $app)

        Setups the templates.

        :type $app: Application
        :param $app: the Application instance of the Silex application

    .. php:method:: setupRoutes(Application $app)

        Setups the routes.

        :type $app: Application
        :param $app: the Application instance of the Silex application
        :returns: mixed the created controller factory

    .. php:method:: setupI18n(Application $app)

        Setups i18n.

        :type $app: Application
        :param $app: the Application instance of the Silex application

    .. php:method:: connect(Application $app)

        Implements ControllerProviderInterface::connect() connecting this
        controller.

        :type $app: Application
        :param $app: the Application instance of the Silex application
        :returns: SilexController\Collection this method is expected to return the used ControllerCollection instance

    .. php:method:: create(Application $app, $entity)

        The controller for the "create" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :returns: Response the HTTP response of this action

    .. php:method:: showList(Application $app, $entity)

        The controller for the "show list" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :returns: Response the HTTP response of this action or 404 on invalid input

    .. php:method:: show(Application $app, $entity, $id)

        The controller for the "show" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id to show
        :returns: Response the HTTP response of this action or 404 on invalid input

    .. php:method:: edit(Application $app, $entity, $id)

        The controller for the "edit" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id to edit
        :returns: Response the HTTP response of this action or 404 on invalid input

    .. php:method:: delete(Application $app, $entity, $id)

        The controller for the "delete" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id to delete
        :returns: Response redirects to the entity list page or 404 on invalid input

    .. php:method:: renderFile(Application $app, $entity, $id, $field)

        The controller for the "render file" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id
        :type $field: string
        :param $field: the field of the file to render of the instance
        :returns: Response the rendered file

    .. php:method:: deleteFile(Application $app, $entity, $id, $field)

        The controller for the "delete file" action.

        :type $app: Application
        :param $app: the Silex application
        :type $entity: string
        :param $entity: the current entity
        :type $id: string
        :param $id: the instance id
        :type $field: string
        :param $field: the field of the file to delete of the instance
        :returns: Response redirects to the instance details page or 404 on invalid input

    .. php:method:: staticFile(Application $app)

        The controller for serving static files.

        :type $app: Application
        :param $app: the Silex application
        :returns: Response redirects to the instance details page or 404 on invalid input

    .. php:method:: setLocale(Application $app, $locale)

        The controller for setting the locale.

        :type $app: Application
        :param $app: the Silex application
        :type $locale: string
        :param $locale: the new locale
        :returns: Response redirects to the instance details page or 404 on invalid input
