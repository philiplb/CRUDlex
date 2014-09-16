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

    protected function getPath($entityName, $entity, $field) {
        return $this->definition->getFilePath($field).'/'.$entityName.'/'.$entity->get('id').'/'.$field;
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

    public function storeFiles($request, $entityName, $entity) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $file = $request->files->get($field);
                $targetPath = $this->getPath($entityName, $entity, $field);
                mkdir($targetPath, 0777, true);
                $file->move($targetPath, $file->getClientOriginalName());
            }
        }
    }

    // For now, we are defensive and don't delete ever.
    public function deleteFile($entity, $entityName, $field) {
        /*
        $targetPath = $this->getPath($entityName, $entity, $field);
        $fileName = $entity->get($field);
        $file = $targetPath.'/'.$fileName;
        if ($fileName && file_exists($file)) {
            unlink($file);
        }
        */
        $entity->set($field, '');
        $this->update($entity);
    }

}
