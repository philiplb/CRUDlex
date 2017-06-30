------------------------
CRUDlex\\ServiceProvider
------------------------

.. php:namespace: CRUDlex

.. php:class:: ServiceProvider

    The ServiceProvider setups and initializes the whole CRUD system.
    After adding it to your Silex-setup, it offers access to AbstractData
    instances, one for each defined entity off the CRUD YAML file.

    .. php:attr:: datas

        protected AbstractData

        Holds the data instances.

    .. php:method:: initMissingServiceProviders(Container $app)

        Initializes needed but yet missing service providers.

        :type $app: Container
        :param $app: the application container

    .. php:method:: initLocales(Container $app)

        Initializes the available locales.

        :type $app: Container
        :param $app: the application container
        :returns: array the available locales

    .. php:method:: initChildren()

        Initializes the children of the data entries.

    .. php:method:: getLocaleLabels($locales, $crud)

        Gets a map with localized entity labels from the CRUD YML.

        :type $locales: array
        :param $locales: the available locales
        :type $crud: array
        :param $crud: the CRUD entity map
        :returns: array the map with localized entity labels

    .. php:method:: configureDefinition(EntityDefinition $definition, $crud)

        Configures the EntityDefinition according to the given
        CRUD entity map.

        :type $definition: EntityDefinition
        :param $definition: the definition to configure
        :type $crud: array
        :param $crud: the CRUD entity map

    .. php:method:: createDefinition(Container $app, $locales, $crud, $name)

        Creates and setups an EntityDefinition instance.

        :type $app: Container
        :param $app: the application container
        :type $locales: array
        :param $locales: the available locales
        :type $crud: array
        :param $crud: the parsed YAML of a CRUD entity
        :type $name: string
        :param $name: the name of the entity
        :returns: EntityDefinition the EntityDefinition good to go

    .. php:method:: validateEntityDefinition(Container $app, $entityDefinition)

        Validates the parsed entity definition.

        :type $app: Container
        :param $app: the application container
        :type $entityDefinition: array
        :param $entityDefinition: the entity definition to validate

    .. php:method:: init($crudFileCachingDirectory, Container $app)

        Initializes the instance.

        :type $crudFileCachingDirectory: string|null
        :param $crudFileCachingDirectory: the writable directory to store the CRUD YAML file cache
        :type $app: Container
        :param $app: the application container

    .. php:method:: register(Container $app)

        Implements ServiceProviderInterface::register() registering $app['crud'].
        $app['crud'] contains an instance of the ServiceProvider afterwards.

        :type $app: Container
        :param $app: the Container instance of the Silex application

    .. php:method:: boot(Application $app)

        Initializes the crud service right after boot.

        :type $app: Application
        :param $app: the Container instance of the Silex application

    .. php:method:: getData($name)

        Getter for the AbstractData instances.

        :type $name: string
        :param $name: the entity name of the desired Data instance
        :returns: AbstractData the AbstractData instance or null on invalid name

    .. php:method:: getEntities()

        Getter for all available entity names.

        :returns: string[] a list of all available entity names

    .. php:method:: getEntitiesNavBar()

        Getter for the entities for the navigation bar.

        :returns: string[] a list of all available entity names with their group

    .. php:method:: getTemplate(Container $app, $section, $action, $entity)

        Determines the Twig template to use for the given parameters depending on
        the existance of certain keys in the Container $app in this order:

        crud.$section.$action.$entity crud.$section.$action crud.$section

        If nothing exists, this string is returned: "@crud/<action>.twig"

        :type $app: Container
        :param $app: the Silex application
        :type $section: string
        :param $section: the section of the template, either "layout" or "template"
        :type $action: string
        :param $action: the current calling action like "create" or "show"
        :type $entity: string
        :param $entity: the current calling entity
        :returns: string the best fitting template

    .. php:method:: setLocale($locale)

        Sets the locale to be used.

        :type $locale: string
        :param $locale: the locale to be used.

    .. php:method:: getLocales()

        Gets the available locales.

        :returns: array the available locales
