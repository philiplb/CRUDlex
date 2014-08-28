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

use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDEntity;

abstract class CRUDData {

    protected $definition;

    protected function hydrate($row) {
        if (!$row) {
            return null;
        }
        $fieldNames = $this->definition->getFieldNames();
        $entity = new CRUDEntity($this->definition);
        foreach ($fieldNames as $fieldName) {
            $entity->set($fieldName, $row[$fieldName]);
        }
        return $entity;
    }

    public abstract function get($id);

    public abstract function listEntries();

    public abstract function create(CRUDEntity $entity);

    public abstract function update(CRUDEntity $entity);

    public abstract function delete($id);

    public abstract function getReferences($table, $nameField);

    public abstract function countBy($table, $params, $paramsOperators, $includeDeleted);

    public abstract function fetchReferences($entity);

    public function getDefinition() {
        return $this->definition;
    }

    public function createEmpty() {
        $entity = new CRUDEntity($this->definition);
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            $entity->set($field, null);
        }
        $entity->set('id', null);
        return $entity;
    }

}
