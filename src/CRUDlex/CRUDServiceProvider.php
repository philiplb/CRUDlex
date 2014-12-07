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
     * Holds the translation map.
     */
    protected $strings;

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
     * Initializes the instance.
     *
     * @param CRUDDataFactoryInterface $dataFactory
     * the factory to create the concrete CRUDData instances
     * @param string $crudFile
     * the CRUD YAML file to parse
     * @param string $stringsFile
     * the YAML file containing the displayed strings
     * @param CRUDFileProcessorInterface $fileProcessor
     * the file processor used for file fields
     */
    public function init(CRUDDataFactoryInterface $dataFactory, $crudFile, $stringsFile, CRUDFileProcessorInterface $fileProcessor) {
        $stringsContent = @file_get_contents($stringsFile);
        if ($stringsContent === false) {
            throw new \Exception('Could not open CRUD strings file');
        }
        $this->strings = Yaml::parse($stringsContent);

        $crudsContent = @file_get_contents($crudFile);
        if ($crudsContent === false) {
            throw new \Exception('Could not open CRUD definition file');
        }
        $cruds = Yaml::parse($crudsContent);
        $this->datas = array();
        foreach ($cruds as $name => $crud) {
            $label = key_exists('label', $crud) ? $crud['label'] : $name;
            $standardFieldLabels = array(
                'id' => $this->translate('label.id'),
                'created_at' => $this->translate('label.created_at'),
                'updated_at' => $this->translate('label.updated_at')
            );
            $listFields = key_exists('listFields', $crud) ? $crud['listFields'] : null;
            $childrenLabelFields = key_exists('childrenLabelFields', $crud) ? $crud['childrenLabelFields'] : array();
            $deleteCascade = key_exists('deleteCascade', $crud) ? $crud['deleteCascade'] : false;
            $pageSize = key_exists('pageSize', $crud) ? $crud['pageSize'] : 25;
            $definition = new CRUDEntityDefinition($crud['table'],
                $crud['fields'],
                $label,
                $listFields,
                $standardFieldLabels,
                $childrenLabelFields,
                $deleteCascade,
                $pageSize,
                $this);
            $this->datas[$name] = $dataFactory->createData($definition, $fileProcessor);
        }

        foreach($this->datas as $name => $data) {
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
            $stringsFile = $app->offsetExists('crud.stringsfile') ? $app['crud.stringsfile'] : __DIR__.'/../strings.yml';
            $fileProcessor = $app->offsetExists('crud.fileprocessor') ? $app['crud.fileprocessor'] : new CRUDSimpleFilesystemFileProcessor();
            $result->init($app['crud.datafactory'], $app['crud.file'], $stringsFile, $fileProcessor);
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
        if (!key_exists($name, $this->datas)) {
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
     * Picks up the string of the given key from the strings and returns the
     * value. Optionally replaces placeholder from "{0}" to "{n}" with the values given via
     * the array $placeholders.
     *
     * @param string $key
     * the key
     * @param array $placeholders
     * the optional placeholders
     *
     * @return string
     * the string value or the key in case there was no string found for the key
     */
    public function translate($key, array $placeholders = array()) {
        if (!key_exists($key, $this->strings)) {
            return $key;
        }
        $result = $this->strings[$key];
        $amount = count($placeholders);
        for ($i = 0; $i < $amount; ++$i) {
            $result = str_replace('{'.$i.'}', $placeholders[$i], $result);
        }
        return $result;
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

}
