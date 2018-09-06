-------------------
CRUDlex\\Controller
-------------------

.. php:namespace: CRUDlex

.. php:class:: Controller

    Default implementation of the ControllerInterface.

    .. php:attr:: filesystem

        protected FilesystemInterface

        Holds the filesystme.

    .. php:attr:: session

        protected SessionInterface

        Holds the session.

    .. php:attr:: translator

        protected TranslatorInterface

        Holds the translator.

    .. php:attr:: service

        protected Service

        Holds the service.

    .. php:attr:: twig

        protected Twig_Environment

        Holds the Twig instance.

    .. php:method:: modifyFilesAndSetFlashBag(Request $request, AbstractData $crudData, Entity $instance, $entity, $mode)

        Postprocesses the entity after modification by handling the uploaded
        files and setting the flash.

        :type $request: Request
        :param $request: the current request
        :type $crudData: AbstractData
        :param $crudData: the data instance of the entity
        :type $instance: Entity
        :param $instance: the entity
        :type $entity: string
        :param $entity: the name of the entity
        :type $mode: string
        :param $mode: whether to 'edit' or to 'create' the entity
        :returns: null|\Symfony\Component\HttpFoundation\RedirectResponse the HTTP response of this modification

    .. php:method:: setValidationFailedFlashes($optimisticLocking, $mode)

        Sets the flashes of a failed entity modification.

        :type $optimisticLocking: boolean
        :param $optimisticLocking: whether the optimistic locking failed
        :type $mode: string
        :param $mode: the modification mode, either 'create' or 'edit'

    .. php:method:: modifyEntity(Request $request, AbstractData $crudData, Entity $instance, $entity, $edit)

        Validates and saves the new or updated entity and returns the appropriate
        HTTP
        response.

        :type $request: Request
        :param $request: the current request
        :type $crudData: AbstractData
        :param $crudData: the data instance of the entity
        :type $instance: Entity
        :param $instance: the entity
        :type $entity: string
        :param $entity: the name of the entity
        :type $edit: boolean
        :param $edit: whether to edit (true) or to create (false) the entity
        :returns: Response the HTTP response of this modification

    .. php:method:: getAfterDeleteRedirectParameters(Request $request, $entity, $redirectPage)

        Gets the parameters for the redirection after deleting an entity.

        :type $request: Request
        :param $request: the current request
        :type $entity: string
        :param $entity: the entity name
        :type $redirectPage: string
        :param $redirectPage: reference, where the page to redirect to will be stored
        :returns: array<string,string> the parameters of the redirection, entity and id

    .. php:method:: buildUpListFilter(Request $request, EntityDefinition $definition, $filter, $filterActive, $filterToUse, $filterOperators)

        Builds up the parameters of the list page filters.

        :type $request: Request
        :param $request: the current request
        :type $definition: EntityDefinition
        :param $definition: the current entity definition
        :param $filter:
        :type $filterActive: boolean
        :param $filterActive: reference, will be true if at least one filter is active
        :type $filterToUse: array
        :param $filterToUse: reference, will hold a map of fields to integers (0 or 1) which boolean filters are active
        :type $filterOperators: array
        :param $filterOperators: reference, will hold a map of fields to operators for AbstractData::listEntries()
        :returns: array the raw filter query parameters

    .. php:method:: getNotFoundPage($error)

        Generates the not found page.

        :type $error: string
        :param $error: the cause of the not found error
        :returns: Response the rendered not found page with the status code 404

    .. php:method:: __construct(Service $service, FilesystemInterface $filesystem, Twig_Environment $twig, SessionInterface $session, TranslatorInterface $translator)

        Controller constructor.

        :type $service: Service
        :param $service: the CRUDlex service
        :type $filesystem: FilesystemInterface
        :param $filesystem: the used filesystem
        :type $twig: Twig_Environment
        :param $twig: the Twig environment
        :type $session: SessionInterface
        :param $session: the session service
        :type $translator: TranslatorInterface
        :param $translator: the translation service

    .. php:method:: setLocaleAndCheckEntity(Request $request)

        {@inheritdoc}

        :type $request: Request
        :param $request:

    .. php:method:: create(Request $request, $entity)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :param $entity:

    .. php:method:: showList(Request $request, $entity)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :param $entity:

    .. php:method:: show($entity, $id)

        {@inheritdoc}

        :param $entity:
        :param $id:

    .. php:method:: edit(Request $request, $entity, $id)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :param $entity:
        :param $id:

    .. php:method:: delete(Request $request, $entity, $id)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :param $entity:
        :param $id:

    .. php:method:: renderFile($entity, $id, $field)

        {@inheritdoc}

        :param $entity:
        :param $id:
        :param $field:

    .. php:method:: deleteFile($entity, $id, $field)

        {@inheritdoc}

        :param $entity:
        :param $id:
        :param $field:

    .. php:method:: staticFile(Request $request)

        {@inheritdoc}

        :type $request: Request
        :param $request:

    .. php:method:: setLocale(Request $request, $locale)

        {@inheritdoc}

        :type $request: Request
        :param $request:
        :param $locale:
