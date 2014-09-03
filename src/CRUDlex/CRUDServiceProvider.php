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

class CRUDServiceProvider implements ServiceProviderInterface {

    protected $datas;

    protected $strings;

    public function init(CRUDDataFactoryInterface $dataFactory, $crudFile, $stringsFile) {
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
            $definition = new CRUDEntityDefinition($crud['table'],
                $crud['fields'],
                $label,
                $listFields,
                $standardFieldLabels);
            $this->datas[$name] = $dataFactory->createData($definition);
        }

        foreach($this->datas as $name => $data) {
            $fields = $data->getDefinition()->getFieldNames();
            foreach ($fields as $field) {
                if ($data->getDefinition()->getType($field) == 'reference') {
                    $this->datas[$data->getDefinition()->getReferenceEntity($field)]->getDefinition()->addParent($data->getDefinition()->getTable(), $field);
                }
            }
        }

    }

    public function register(Application $app) {
        $app['crud'] = $app->share(function() use ($app) {
            $result = new CRUDServiceProvider();
            $stringsFile = $app->offsetExists('crud.stringsfile') ? $app['crud.stringsfile'] : __DIR__.'/../strings.yml';
            $result->init($app['crud.datafactory'], $app['crud.file'], $stringsFile);
            return $result;
        });
    }

    public function boot(Application $app) {
    }

    public function getData($name) {
        if (!key_exists($name, $this->datas)) {
            return null;
        }
        return $this->datas[$name];
    }

    public function getEntities() {
        return array_keys($this->datas);
    }

    public function formatDate($value) {
        if (!$value) {
            return '';
        }
        $result = \DateTime::createFromFormat('Y-m-d', $value);
        if ($result === false) {
            $result = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }
        if ($result === false) {
            return $value;
        }
        return $result->format('Y-m-d');
    }

    public function formatDateTime($value) {
        if (!$value) {
            return '';
        }
        $result = \DateTime::createFromFormat('Y-m-d H:i', $value);
        if ($result === false) {
            $result = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }
        if ($result === false) {
            return $value;
        }
        return $result->format('Y-m-d H:i');
    }

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

}
