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

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Translation\Loader\YamlFileLoader;

use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDDataFactoryInterface;
use CRUDlex\CRUDEntity;
use CRUDlex\CRUDFileProcessorInterface;
use CRUDlex\CRUDSimpleFilesystemFileProcessor;

/**
 * The CRUDServiceProvider setups and initializes the whole CRUD system.
 * After adding it to your Silex-setup, it offers access to {@see CRUDData}
 * instances, one for each defined entity off the CRUD YAML file.
 */
class CRUDServiceProvider implements ServiceProviderInterface {

    /**
     * Holds the {@see CRUDData} instances.
     */
    protected $datas;

    /**
     * Holds whether we manage the i18n.
     */
    protected $manageI18n;

    /**
     * Formats the given time value to a timestring defined by the $pattern
     * parameter.
     *
     * If the value is false (like null), an empty string is
     * returned. Else, the value is tried to be parsed as datetime via the
     * given pattern. If that fails, it is tried to be parsed with the pattern
     * 'Y-m-d H:i:s'. If that fails, the value is returned unchanged. Else, it
     * is returned formatted with the given pattern. The effect is to shorten
     * 'Y-m-d H:i:s' to 'Y-m-d' for example.
     *
     * @param string $value
     * the value to be formatted
     * @param string $pattern
     * the pattern with which the value is parsed and formatted
     *
     * @return string
     * the formatted value
     */
    protected function formatTime($value, $pattern) {
        if (!$value) {
            return '';
        }
        $result = \DateTime::createFromFormat($pattern, $value);
        if ($result === false) {
            $result = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }
        if ($result === false) {
            return $value;
        }
        return $result->format($pattern);
    }

    /**
     * Reads and returns the contents of the given file. If
     * it goes wrong, it throws an exception.
     *
     * @param string $fileName
     * the file to read
     *
     * @return string
     * the file contents
     */
    protected function readYaml($fileName) {
        if (!file_exists($fileName) || !is_readable($fileName) || !is_file($fileName)) {
            throw new \Exception('Could not open CRUD file '.$fileName);
        }
        $fileContent = file_get_contents($fileName);
        return Yaml::parse($fileContent);
    }

    /**
     * Initializes the instance.
     *
     * @param CRUDDataFactoryInterface $dataFactory
     * the factory to create the concrete CRUDData instances
     * @param string $crudFile
     * the CRUD YAML file to parse
     * @param CRUDFileProcessorInterface $fileProcessor
     * the file processor used for file fields
     * @param boolean $manageI18n
     * holds whether we manage the i18n
     * @param Application $app
     * the application container
     */
    public function init(CRUDDataFactoryInterface $dataFactory, $crudFile, CRUDFileProcessorInterface $fileProcessor, $manageI18n, Application $app) {

        $this->manageI18n = $manageI18n;
        if (!$app->offsetExists('translator')) {
            $app->register(new \Silex\Provider\TranslationServiceProvider(), array(
                'locale_fallbacks' => array('en'),
            ));
        }

        if (!$app->offsetExists('session')) {
            $app->register(new \Silex\Provider\SessionServiceProvider());
        }

        if (!$app->offsetExists('url_generator')) {
            $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        }

        if (!$app->offsetExists('twig')) {
            $app->register(new \Silex\Provider\TwigServiceProvider());
            $app['twig.loader.filesystem']->addPath(__DIR__ . '/../views/', 'crud');
        }

        $app['translator']->addLoader('yaml', new YamlFileLoader());
        $localeDir = __DIR__.'/../locales';
        $langFiles = scandir($localeDir);
        $locales = array();
        foreach ($langFiles as $langFile) {
            if ($langFile == '.' || $langFile == '..') {
                continue;
            }
            $locale = substr($langFile, 0, strpos($langFile, '.yml'));
            $locales[] = $locale;
            $app['translator']->addResource('yaml', $localeDir.'/'.$langFile, $locale);
        }

        $parsedYaml = $this->readYaml($crudFile);

        $this->datas = array();
        foreach ((empty($parsedYaml) ? array() : $parsedYaml) as $name => $crud) {
            if (!is_array($crud) || !isset($crud['fields'])) continue;

            $label = array_key_exists('label', $crud) ? $crud['label'] : $name;

            $localeLabels = array();
            foreach ($locales as $locale) {
                if (array_key_exists('label_' . $locale, $crud)) {
                    $localeLabels[$locale] = $crud['label_'.$locale];
                }
            }

            $standardFieldLabels = array(
                'id' => $app['translator']->trans('crudlex.label.id'),
                'created_at' => $app['translator']->trans('crudlex.label.created_at'),
                'updated_at' => $app['translator']->trans('crudlex.label.updated_at')
            );

            $definition = new CRUDEntityDefinition(
                $crud['table'],
                $crud['fields'],
                $label,
                $localeLabels,
                $standardFieldLabels,
                $this
            );
            $this->datas[$name] = $dataFactory->createData($definition, $fileProcessor);

            if (array_key_exists('deleteCascade', $crud)) {
                $this->datas[$name]->getDefinition()->setDeleteCascade($crud['deleteCascade']);
            }
            if (array_key_exists('listFields', $crud)) {
                $this->datas[$name]->getDefinition()->setListFieldNames($crud['listFields']);
            }
            if (array_key_exists('filter', $crud)) {
                $this->datas[$name]->getDefinition()->setFilter($crud['filter']);
            }
            if (array_key_exists('childrenLabelFields', $crud)) {
                $this->datas[$name]->getDefinition()->setChildrenLabelFields($crud['childrenLabelFields']);
            }
            if (array_key_exists('pageSize', $crud)) {
                $this->datas[$name]->getDefinition()->setPageSize($crud['pageSize']);
            }

        }

        foreach ($this->datas as $name => $data) {
            $fields = $data->getDefinition()->getFieldNames();
            foreach ($fields as $field) {
                if ($data->getDefinition()->getType($field) == 'reference') {
                    $this->datas[$data->getDefinition()->getReferenceEntity($field)]->getDefinition()->addChild($data->getDefinition()->getTable(), $field, $name);
                }
            }
        }

    }

    /**
     * Implements ServiceProviderInterface::register() registering $app['crud'].
     * $app['crud'] contains an instance of the CRUDServiceProvider afterwards.
     *
     * @param Application $app
     * the Application instance of the Silex application
     */
    public function register(Application $app) {
        $app['crud'] = $app->share(function() use ($app) {
            $result = new CRUDServiceProvider();
            $fileProcessor = $app->offsetExists('crud.fileprocessor') ? $app['crud.fileprocessor'] : new CRUDSimpleFilesystemFileProcessor();
            $manageI18n = $app->offsetExists('crud.manageI18n') ? $app['crud.manageI18n'] : true;
            $result->init($app['crud.datafactory'], $app['crud.file'], $fileProcessor, $manageI18n, $app);
            return $result;
        });
    }

    /**
     * Implements ServiceProviderInterface::boot().
     *
     * @param Application $app
     * the Application instance of the Silex application
     */
    public function boot(Application $app) {
    }

    /**
     * Getter for the {@see CRUDData} instances.
     *
     * @param string $name
     * the entity name of the desired CRUDData instance
     *
     * @return CRUDData
     * the CRUDData instance or null on invalid name
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
     * @return array
     * a list of all available entity names
     */
    public function getEntities() {
        return array_keys($this->datas);
    }

    /**
     * Formats the given value to a date of the format 'Y-m-d'.
     *
     * @param string $value
     * the value, might be of the format 'Y-m-d H:i' or 'Y-m-d'
     *
     * @return string
     * the formatted result or an empty string on null value
     */
    public function formatDate($value) {
        return $this->formatTime($value, 'Y-m-d');
    }

    /**
     * Formats the given value to a date of the format 'Y-m-d H:i'.
     *
     * @param string $value
     * the value, might be of the format 'Y-m-d H:i'
     *
     * @return string
     * the formatted result or an empty string on null value
     */
    public function formatDateTime($value) {
        return $this->formatTime($value, 'Y-m-d H:i');
    }

    /**
     * Calls PHPs
     * {@link http://php.net/manual/en/function.basename.php basename} and
     * returns it's result.
     *
     * @param string $value
     * the value to be handed to basename
     *
     * @return string
     * the result of basename
     */
    public function basename($value) {
        return basename($value);
    }

    /**
     * Determines the Twig template to use for the given parameters depending on
     * the existance of certain keys in the Application $app in this order:
     *
     * crud.$section.$action.$entity
     * crud.$section.$action
     * crud.$section
     *
     * If nothing exists, this string is returned: "@crud/<action>.twig"
     *
     * @param Application $app
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
    public function getTemplate(Application $app, $section, $action, $entity) {
        if ($app->offsetExists('crud.'.$section.'.'.$action.'.'.$entity)) {
            return $app['crud.'.$section.'.'.$action.'.'.$entity];
        }
        if ($app->offsetExists('crud.'.$section.'.'.$entity)) {
            return $app['crud.'.$section.'.'.$entity];
        }
        if ($app->offsetExists('crud.'.$section.'.'.$action)) {
            return $app['crud.'.$section.'.'.$action];
        }
        if ($app->offsetExists('crud.'.$section)) {
            return $app['crud.'.$section];
        }

        return '@crud/'.$action.'.twig';
    }

    /**
     * Gets whether CRUDlex manages the i18n system.
     *
     * @return boolean
     * true if CRUDlex manages the i18n system
     */
    public function getManageI18n() {
        return $this->manageI18n;
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
     * Formats a float to not display in scientific notation.
     *
     * @param float $float
     * the float to format
     *
     * @return string
     * the formated float
     */
    public function formatFloat($float) {

        if (!$float) {
            return $float;
        }

        $zeroFraction = $float - floor($float) == 0 ? '0' : '';

        // We don't want values like 0.004 converted to  0.00400000000000000008
    	if ($float > 0.0001) {
    		return $float . ($zeroFraction === '0' ? '.'.$zeroFraction : '');
    	}

        // We don't want values like 0.00004 converted to its scientific notation 4.0E-5
        return rtrim(sprintf('%.20F', $float), '0').$zeroFraction;
    }

}
