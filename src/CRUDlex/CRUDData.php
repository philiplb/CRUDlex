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

use CRUDlex\CRUDEntityDefinition;
use CRUDlex\CRUDEntity;
use Symfony\Component\HttpFoundation\Request;

abstract class CRUDData {

    protected $definition;

    protected $fileProcessor;

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

    public function createFiles(Request $request, CRUDEntity $entity, $entityName) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $this->fileProcessor->createFile($request, $entity, $entityName, $field);
            }
        }
    }

    public function updateFiles(Request $request, CRUDEntity $entity, $entityName) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $this->fileProcessor->updateFile($request, $entity, $entityName, $field);
            }
        }
    }

    public function deleteFile(CRUDEntity $entity, $entityName, $field) {
        $this->fileProcessor->deleteFile($entity, $entityName, $field);
    }

    public function deleteFiles(CRUDEntity $entity, $entityName) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $this->fileProcessor->deleteFile($entity, $entityName, $field);
            }
        }
    }

}
