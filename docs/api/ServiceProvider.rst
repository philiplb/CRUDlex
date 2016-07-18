------------------------
CRUDlex\\ServiceProvider
------------------------

.. php:namespace: CRUDlex

.. php:class:: ServiceProvider

    The ServiceProvider setups and initializes the whole CRUD system.
    After adding it to your Silex-setup, it offers access to {@see AbstractData}
    instances, one for each defined entity off the CRUD YAML file.

    .. php:attr:: datas

        protected

        Holds the {@see AbstractData} instances.

    .. php:attr:: manageI18n

        protected

        Holds whether we manage the i18n.

    .. php:method:: formatTime($value, $timezone, $pattern)

        Formats the given time value to a timestring defined by the $pattern
        parameter.

        If the value is false (like null), an empty string is returned. Else, the
        value is tried to be parsed as datetime via the given pattern. If that
        fails, it is tried to be parsed with the pattern
        'Y-m-d H:i:s'. If that fails, the value is returned unchanged. Else, it is
        returned formatted with the given pattern. The effect is to shorten
        'Y-m-d H:i:s' to 'Y-m-d' for example.

        :type $value: string
        :param $value: the value to be formatted
        :type $timezone: string
        :param $timezone: the timezone of the value
        :type $pattern: string
        :param $pattern: the pattern with which the value is parsed and formatted
        :returns: string the formatted value

    .. php:method:: readYaml($fileName)

        Reads and returns the contents of the given Yaml file. If
        it goes wrong, it throws an exception.

        :type $fileName: string
        :param $fileName: the file to read
        :returns: array the file contents

    .. php:method:: initMissingServiceProviders(Application $app)

        Initializes needed but yet missing service providers.

        :type $app: Application
        :param $app: the application container

    .. php:method:: initLocales(Application $app)

        Initializes the available locales.

        :type $app: Application
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

    .. php:method:: createDefinition(Application $app, $locales, $crud, $name)

        Creates and setups an EntityDefinition instance.

        :type $app: Application
        :param $app: the application container
        :type $locales: array
        :param $locales: the available locales
        :type $crud: array
        :param $crud: the parsed YAML of a CRUD entity
        :type $name: string
        :param $name: the name of the entity
        :returns: EntityDefinition the EntityDefinition good to go

    .. php:method:: init(DataFactoryInterface $dataFactory, $crudFile, FileProcessorInterface $fileProcessor, $manageI18n, Application $app)

        Initializes the instance.

        :type $dataFactory: DataFactoryInterface
        :param $dataFactory: the factory to create the concrete AbstractData instances
        :type $crudFile: string
        :param $crudFile: the CRUD YAML file to parse
        :type $fileProcessor: FileProcessorInterface
        :param $fileProcessor: the file processor used for file fields
        :type $manageI18n: boolean
        :param $manageI18n: holds whether we manage the i18n
        :type $app: Application
        :param $app: the application container

    .. php:method:: register(Application $app)

        Implements ServiceProviderInterface::register() registering $app['crud'].
        $app['crud'] contains an instance of the ServiceProvider afterwards.

        :type $app: Application
        :param $app: the Application instance of the Silex application

    .. php:method:: boot(Application $app)

        Implements ServiceProviderInterface::boot().

        :type $app: Application
        :param $app: the Application instance of the Silex application

    .. php:method:: getData($name)

        Getter for the {@see AbstractData} instances.

        :type $name: string
        :param $name: the entity name of the desired Data instance
        :returns: AbstractData the AbstractData instance or null on invalid name

    .. php:method:: getEntities()

        Getter for all available entity names.

        :returns: string[] a list of all available entity names

    .. php:method:: formatDate($value, $isUTC)

        Formats the given value to a date of the format 'Y-m-d'.

        :type $value: string
        :param $value: the value, might be of the format 'Y-m-d H:i' or 'Y-m-d'
        :type $isUTC: boolean
        :param $isUTC: whether the given value is in UTC
        :returns: string the formatted result or an empty string on null value

    .. php:method:: formatDateTime($value, $isUTC)

        Formats the given value to a date of the format 'Y-m-d H:i'.

        :type $value: string
        :param $value: the value, might be of the format 'Y-m-d H:i'
        :type $isUTC: boolean
        :param $isUTC: whether the given value is in UTC
        :returns: string the formatted result or an empty string on null value

    .. php:method:: basename($value)

        Calls PHPs
        {@link http://php.net/manual/en/function.basename.php basename} and
        returns it's result.

        :type $value: string
        :param $value: the value to be handed to basename
        :returns: string the result of basename

    .. php:method:: getTemplate(Application $app, $section, $action, $entity)

        Determines the Twig template to use for the given parameters depending on
        the existance of certain keys in the Application $app in this order:

        crud.$section.$action.$entity crud.$section.$action crud.$section

        If nothing exists, this string is returned: "@crud/<action>.twig"

        :type $app: Application
        :param $app: the Silex application
        :type $section: string
        :param $section: the section of the template, either "layout" or "template"
        :type $action: string
        :param $action: the current calling action like "create" or "show"
        :type $entity: string
        :param $entity: the current calling entity
        :returns: string the best fitting template

    .. php:method:: isManagingI18n()

        Gets whether CRUDlex manages the i18n system.

        :returns: boolean true if CRUDlex manages the i18n system

    .. php:method:: setLocale($locale)

        Sets the locale to be used.

        :type $locale: string
        :param $locale: the locale to be used.

    .. php:method:: getLocales()

        Gets the available locales.

        :returns: array the available locales

    .. php:method:: getLanguageName($language)

        Gets a language name in the given language.

        :type $language: string
        :param $language: the language code of the desired language name
        :returns: string the language name in the given language or null if not available

    .. php:method:: formatFloat($float)

        Formats a float to not display in scientific notation.

        :type $float: float
        :param $float: the float to format
        :returns: double|string the formated float
