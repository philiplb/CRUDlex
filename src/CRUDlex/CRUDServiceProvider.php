<?php

/*
 * This file is part of the Crudlex package.
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
        $cruds = Yaml::parse(file_get_contents($crudFile));
        $this->datas = array();
        foreach ($cruds as $name => $crud) {
            $definition = new CRUDEntityDefinition($crud['table'], $crud['fields']);
            $this->datas[$name] = $dataFactory->createData($definition);
        }

        foreach($this->datas as $name => $data) {
            $fields = $data->getDefinition()->getFieldNames();
            foreach ($fields as $field) {
                if ($definition->getType($field) == 'reference') {
                    $this->datas[$data->getDefinition()->getReferenceEntity($field)]->getDefinition()->addParent($data->getDefinition()->getTable(), $field);
                }
            }
        }

        $this->strings = Yaml::parse(file_get_contents($stringsFile));
    }

    public function register(Application $app) {
        $app['crud'] = $app->share(function() use ($app) {
            $result = new CRUDServiceProvider();
            $stringsFile = $app->offsetExists('crud.stringsfile') ? $app['crud.stringsfile'] : __DIR__.'/strings.yml';
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
        $dateTime = new \DateTime($value);
        return $dateTime->format('Y-m-d');
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
