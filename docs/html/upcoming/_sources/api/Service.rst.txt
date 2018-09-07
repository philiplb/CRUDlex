----------------
CRUDlex\\Service
----------------

.. php:namespace: CRUDlex

.. php:class:: Service

    The Service setups and initializes the whole CRUD system and is initialized via the framework
    specific implementation, the Silex one for example.
    It offers access to AbstractData instances, one for each defined entity off the CRUD YAML file
    and various other helper functions.

    .. php:attr:: datas

        protected array

        Holds the data instances.

    .. php:attr:: templates

        protected array

        Holds the map for overriding templates.

    .. php:attr:: manageI18n

        protected bool

        Holds whether CRUDlex manages i18n.

    .. php:attr:: urlGenerator

        protected \Symfony\Component\Routing\Generator\UrlGeneratorInterface

        Holds the URL generator.

    .. php:method:: getLocales()

        Gets the available locales.

        :returns: array the available locales

    .. php:method:: initChildren()

        Initializes the children of the data entries.

    .. php:method:: getLocaleLabels($crud)

        Gets a map with localized entity labels from the CRUD YML.

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

    .. php:method:: createDefinition(TranslatorInterface $translator, EntityDefinitionFactoryInterface $entityDefinitionFactory, $crud, $name)

        Creates and setups an EntityDefinition instance.

        :type $translator: TranslatorInterface
        :param $translator: the Translator to use for some standard field labels
        :type $entityDefinitionFactory: EntityDefinitionFactoryInterface
        :param $entityDefinitionFactory: the EntityDefinitionFactory to use
        :type $crud: array
        :param $crud: the parsed YAML of a CRUD entity
        :type $name: string
        :param $name: the name of the entity
        :returns: EntityDefinition the EntityDefinition good to go

    .. php:method:: __construct($crudFile, $crudFileCachingDirectory, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, DataFactoryInterface $dataFactory, EntityDefinitionFactoryInterface $entityDefinitionFactory, FilesystemInterface $filesystem, EntityDefinitionValidatorInterface $validator)

        Initializes the instance.

        :type $crudFile: string
        :param $crudFile: the CRUD YAML file
        :type $crudFileCachingDirectory: string|null
        :param $crudFileCachingDirectory: the writable directory to store the CRUD YAML file cache
        :type $urlGenerator: UrlGeneratorInterface
        :param $urlGenerator: the URL generator to use
        :type $translator: TranslatorInterface
        :param $translator: the translator to use
        :type $dataFactory: DataFactoryInterface
        :param $dataFactory: the data factory to use
        :type $entityDefinitionFactory: EntityDefinitionFactoryInterface
        :param $entityDefinitionFactory: the EntityDefinitionFactory to use
        :type $filesystem: FilesystemInterface
        :param $filesystem: the filesystem to use
        :type $validator: EntityDefinitionValidatorInterface
        :param $validator: the validator to use, null if no validation required

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

    .. php:method:: setTemplate($key, $template)

        Sets a template to use instead of the build in ones.

        :type $key: string
        :param $key: the template key to use in this format: $section.$action.$entity $section.$action $section
        :type $template: string
        :param $template: the template to use for this key

    .. php:method:: getTemplate($section, $action, $entity)

        Determines the Twig template to use for the given parameters depending on
        the existance of certain template keys set in this order:

        $section.$action.$entity
        $section.$action
        $section

        If nothing exists, this string is returned: "@crud/<action>.twig"

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

    .. php:method:: isManageI18n()

        Gets whether CRUDlex manages the i18n.

        :returns: bool true if so

    .. php:method:: setManageI18n($manageI18n)

        Sets whether CRUDlex manages the i18n.

        :type $manageI18n: bool
        :param $manageI18n: true if so

    .. php:method:: generateURL($name, $parameters)

        Generates an URL.

        :type $name: string
        :param $name: the name of the route
        :type $parameters: mixed
        :param $parameters: an array of parameters
        :returns: null|string the generated URL
