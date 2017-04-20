<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlex;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;

/**
 * The ServiceProvider setups and initializes the whole CRUD system.
 * After adding it to your Silex-setup, it offers access to {@see AbstractData}
 * instances, one for each defined entity off the CRUD YAML file.
 */
class ServiceProvider implements ServiceProviderInterface, BootableProviderInterface {

    /**
     * Holds the {@see AbstractData} instances.
     */
    protected $datas;

    /**
     * Initializes needed but yet missing service providers.
     *
     * @param Container $app
     * the application container
     */
    protected function initMissingServiceProviders(Container $app) {

        if (!$app->offsetExists('translator')) {
            $app->register(new LocaleServiceProvider());
            $app->register(new TranslationServiceProvider(), [
                'locale_fallbacks' => ['en'],
            ]);
        }

        if (!$app->offsetExists('session')) {
            $app->register(new SessionServiceProvider());
        }

        if (!$app->offsetExists('twig')) {
            $app->register(new TwigServiceProvider());
        }
        $app['twig.loader.filesystem']->addPath(__DIR__.'/../views/', 'crud');
    }

    /**
     * Initializes the available locales.
     *
     * @param Container $app
     * the application container
     *
     * @return array
     * the available locales
     */
    protected function initLocales(Container $app) {
        $locales   = $this->getLocales();
        $localeDir = __DIR__.'/../locales';
        $app['translator']->addLoader('yaml', new YamlFileLoader());
        foreach ($locales as $locale) {
            $app['translator']->addResource('yaml', $localeDir.'/'.$locale.'.yml', $locale);
        }
        return $locales;
    }

    /**
     * Initializes the children of the data entries.
     */
    protected function initChildren() {
        foreach ($this->datas as $name => $data) {
            $fields = $data->getDefinition()->getFieldNames();
            foreach ($fields as $field) {
                if ($data->getDefinition()->getType($field) == 'reference') {
                    $this->datas[$data->getDefinition()->getSubTypeField($field, 'reference', 'entity')]->getDefinition()->addChild($data->getDefinition()->getTable(), $field, $name);
                }
            }
        }
    }

    /**
     * Gets a map with localized entity labels from the CRUD YML.
     *
     * @param array $locales
     * the available locales
     * @param array $crud
     * the CRUD entity map
     *
     * @return array
     * the map with localized entity labels
     */
    protected function getLocaleLabels($locales, $crud) {
        $localeLabels = [];
        foreach ($locales as $locale) {
            if (array_key_exists('label_'.$locale, $crud)) {
                $localeLabels[$locale] = $crud['label_'.$locale];
            }
        }
        return $localeLabels;
    }

    /**
     * Configures the EntityDefinition according to the given
     * CRUD entity map.
     *
     * @param EntityDefinition $definition
     * the definition to configure
     * @param array $crud
     * the CRUD entity map
     */
    protected function configureDefinition(EntityDefinition $definition, array $crud) {
        $toConfigure = [
            'deleteCascade',
            'listFields',
            'filter',
            'childrenLabelFields',
            'pageSize',
            'initialSortField',
            'initialSortAscending',
            'navBarGroup'
        ];
        foreach ($toConfigure as $field) {
            if (array_key_exists($field, $crud)) {
                $function = 'set'.ucfirst($field);
                $definition->$function($crud[$field]);
            }
        }
    }

    /**
     * Creates and setups an EntityDefinition instance.
     *
     * @param Container $app
     * the application container
     * @param array $locales
     * the available locales
     * @param array $crud
     * the parsed YAML of a CRUD entity
     * @param string $name
     * the name of the entity
     *
     * @return EntityDefinition
     * the EntityDefinition good to go
     */
    protected function createDefinition(Container $app, array $locales, array $crud, $name) {
        $label               = array_key_exists('label', $crud) ? $crud['label'] : $name;
        $localeLabels        = $this->getLocaleLabels($locales, $crud);
        $standardFieldLabels = [
            'id' => $app['translator']->trans('crudlex.label.id'),
            'created_at' => $app['translator']->trans('crudlex.label.created_at'),
            'updated_at' => $app['translator']->trans('crudlex.label.updated_at')
        ];

        $factory = $app->offsetExists('crud.entitydefinitionfactory') ? $app['crud.entitydefinitionfactory'] : new EntityDefinitionFactory();

        $definition = $factory->createEntityDefinition(
            $crud['table'],
            $crud['fields'],
            $label,
            $localeLabels,
            $standardFieldLabels,
            $this
        );
        $this->configureDefinition($definition, $crud);
        return $definition;
    }

    /**
     * Validates the parsed entity definition.
     *
     * @param Container $app
     * the application container
     * @param array $entityDefinition
     * the entity definition to validate
     */
    protected function validateEntityDefinition(Container $app, array $entityDefinition) {
        $doValidate = !$app->offsetExists('crud.validateentitydefinition') || $app['crud.validateentitydefinition'] === true;
        if ($doValidate) {
            $validator = $app->offsetExists('crud.entitydefinitionvalidator')
                ? $app['crud.entitydefinitionvalidator']
                : new EntityDefinitionValidator();
            $validator->validate($entityDefinition);
        }
    }

    /**
     * Initializes the instance.
     *
     * @param DataFactoryInterface $dataFactory
     * the factory to create the concrete AbstractData instances
     * @param string $crudFile
     * the CRUD YAML file to parse
     * @param string|null $crudFileCachingDirectory
     * the writable directory to store the CRUD YAML file cache
     * @param FileProcessorInterface $fileProcessor
     * the file processor used for file fields
     * @param Container $app
     * the application container
     */
    public function init(DataFactoryInterface $dataFactory, $crudFile, $crudFileCachingDirectory, FileProcessorInterface $fileProcessor, Container $app) {

        $reader     = new YamlReader($crudFileCachingDirectory);
        $parsedYaml = $reader->read($crudFile);

        $this->validateEntityDefinition($app, $parsedYaml);

        $locales     = $this->initLocales($app);
        $this->datas = [];
        foreach ($parsedYaml as $name => $crud) {
            $definition         = $this->createDefinition($app, $locales, $crud, $name);
            $this->datas[$name] = $dataFactory->createData($definition, $fileProcessor);
        }

        $this->initChildren();

    }

    /**
     * Implements ServiceProviderInterface::register() registering $app['crud'].
     * $app['crud'] contains an instance of the ServiceProvider afterwards.
     *
     * @param Container $app
     * the Container instance of the Silex application
     */
    public function register(Container $app) {
        $app['crud'] = function() use ($app) {
            $result                   = new static();
            $crudFileCachingDirectory = $app->offsetExists('crud.filecachingdirectory') ? $app['crud.filecachingdirectory'] : null;
            $fileProcessor            = $app->offsetExists('crud.fileprocessor') ? $app['crud.fileprocessor'] : new SimpleFilesystemFileProcessor();
            $result->init($app['crud.datafactory'], $app['crud.file'], $crudFileCachingDirectory, $fileProcessor, $app);
            return $result;
        };
    }

    /**
     * Initializes the crud service right after boot.
     *
     * @param Application $app
     * the Container instance of the Silex application
     */
    public function boot(Application $app) {
        $this->initMissingServiceProviders($app);
        $twigExtensions = new TwigExtensions();
        $twigExtensions->registerTwigExtensions($app);
    }

    /**
     * Getter for the {@see AbstractData} instances.
     *
     * @param string $name
     * the entity name of the desired Data instance
     *
     * @return AbstractData
     * the AbstractData instance or null on invalid name
     */
    public function getData($name) {
        if (!array_key_exists($name, $this->datas)) {
            return null;
        }
        return $this->datas[$name];
    }

    /**
     * Getter for all available entity names.
     *
     * @return string[]
     * a list of all available entity names
     */
    public function getEntities() {
        return array_keys($this->datas);
    }

    /**
     * Getter for the entitis fot the navigation bar.
     *
     * @return string[]
     * a list of all available entity names with theirs group
     */
    public function getEntitiesNavBar() {
      foreach ($this->datas as $entity => $data) {
        $navBarGroup = $data->getDefinition()->getNavBarGroup();
        if ($navBarGroup !== 'main'){
          $result[$navBarGroup][] = $entity;
        }else{
          $result[$entity] = 'main';
        }
      }
        return $result;
    }

    /**
     * Determines the Twig template to use for the given parameters depending on
     * the existance of certain keys in the Container $app in this order:
     *
     * crud.$section.$action.$entity
     * crud.$section.$action
     * crud.$section
     *
     * If nothing exists, this string is returned: "@crud/<action>.twig"
     *
     * @param Container $app
     * the Silex application
     * @param string $section
     * the section of the template, either "layout" or "template"
     * @param string $action
     * the current calling action like "create" or "show"
     * @param string $entity
     * the current calling entity
     *
     * @return string
     * the best fitting template
     */
    public function getTemplate(Container $app, $section, $action, $entity) {
        $crudSection       = 'crud.'.$section;
        $crudSectionAction = $crudSection.'.'.$action;

        $offsets = [
            $crudSectionAction.'.'.$entity,
            $crudSection.'.'.$entity,
            $crudSectionAction,
            $crudSection
        ];
        foreach ($offsets as $offset) {
            if ($app->offsetExists($offset)) {
                return $app[$offset];
            }
        }

        return '@crud/'.$action.'.twig';
    }

    /**
     * Sets the locale to be used.
     *
     * @param string $locale
     * the locale to be used.
     */
    public function setLocale($locale) {
        foreach ($this->datas as $data) {
            $data->getDefinition()->setLocale($locale);
        }
    }

    /**
     * Gets the available locales.
     *
     * @return array
     * the available locales
     */
    public function getLocales() {
        $localeDir     = __DIR__.'/../locales';
        $languageFiles = scandir($localeDir);
        $locales       = [];
        foreach ($languageFiles as $languageFile) {
            if (in_array($languageFile, ['.', '..'])) {
                continue;
            }
            $extensionPos = strpos($languageFile, '.yml');
            if ($extensionPos !== false) {
                $locale    = substr($languageFile, 0, $extensionPos);
                $locales[] = $locale;
            }
        }
        sort($locales);
        return $locales;
    }

}
