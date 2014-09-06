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

use CRUDlex\CRUDEntity;
use CRUDlex\CRUDData;

class CRUDMySQLData extends CRUDData {

    protected $db;

    public function __construct(CRUDEntityDefinition $definition, $db) {
        $this->db = $db;
        $this->definition = $definition;
    }

    public function get($id) {
        $sql = 'SELECT `'.implode('`,`', $this->definition->getFieldNames()).'` FROM '.$this->definition->getTable();
        $sql .= ' WHERE id = ? AND deleted_at IS NULL';
        $row = $this->db->fetchAssoc($sql, array($id));
        $entity = $this->hydrate($row);
        return $entity;
    }

    public function listEntries() {
        $fieldNames = $this->definition->getFieldNames();
        $sql = 'SELECT `'.implode('`,`', $fieldNames).'`';
        $sql .= ' FROM '.$this->definition->getTable().' WHERE deleted_at IS NULL';
        $rows = $this->db->fetchAll($sql);
        $entities = array();
        foreach ($rows as $row) {
            $entities[] = $this->hydrate($row);
        }
        return $entities;
    }

    public function create(CRUDEntity $entity) {
        $formFields = $this->definition->getEditableFieldNames();
        $fields = array_merge(array('created_at', 'updated_at', 'version'),
                $formFields);
        $placeHolders = array();
        $values = array();
        for ($i = 0; $i < count($formFields); ++$i) {
            $placeHolders[] = '?';
            $value = $entity->get($formFields[$i]);
            if ($this->definition->getType($formFields[$i]) == 'bool') {
                $value = $value ? 1 : 0;
            }
            $values[] = $value;
        }
        $sql = 'INSERT INTO '.$this->definition->getTable().' (`'.implode('`,`', $fields).'`) VALUES (NOW(), NOW(), 0, '.implode(',', $placeHolders).')';
        $this->db->executeUpdate($sql, $values);

        $entity->set('id', $this->db->lastInsertId());
    }

    public function update(CRUDEntity $entity) {
        $formFields = $this->definition->getEditableFieldNames();
        $fields = array_merge(array('updated_at', 'version'),
                $formFields);
        $values = array();
        $sets = array();
        for ($i = 0; $i < count($formFields); ++$i) {
            $value = $entity->get($formFields[$i]);
            if ($this->definition->getType($formFields[$i]) == 'bool') {
                $value = $value ? 1 : 0;
            }
            $values[] = $value;
            $sets[] = '`'.$formFields[$i].'`=?';
        }
        $values[] = $entity->get('id');
        $sql = 'UPDATE '.$this->definition->getTable().' SET updated_at = NOW(), ';
        $sql .= implode(',', $sets).' WHERE id=?';
        $this->db->executeUpdate($sql, $values);

        return $this->db->lastInsertId();
    }

    public function delete($id) {
        foreach ($this->definition->getParents() as $parent) {
            $sql = 'SELECT COUNT(id) AS amount FROM '.$parent[0].' WHERE ';
            $sql .= $parent[1].' = ? AND deleted_at IS NULL';
            $result = $this->db->fetchAssoc($sql, array($id));
            if ($result['amount'] > 0) {
                return false;
            }
        }

        $sql = 'UPDATE '.$this->definition->getTable().' SET deleted_at = NOW() WHERE id = ?';
        $this->db->executeUpdate($sql, array($id));
        return true;
    }

    public function getReferences($table, $nameField) {
        $sql = 'SELECT id, `'.$nameField.'` FROM '.$table.' WHERE deleted_at IS NULL ORDER BY `'.$nameField.'`';
        $entries = $this->db->fetchAll($sql);
        $result = array();
        foreach ($entries as $entry) {
            $result[$entry['id']] = $entry[$nameField];
        }
        return $result;
    }

    public function countBy($table, $params, $paramsOperators, $includeDeleted) {
        $sql = 'SELECT COUNT(id) AS amount FROM '.$table;
        $paramValues = array();
        $paramSQLs = array();
        foreach($params as $name => $value) {
            $paramSQLs[] = '`'.$name.'`'.$paramsOperators[$name].'?';
            $paramValues[] = $value;
        }
        $sql .= ' WHERE '.implode(' AND ', $paramSQLs);
        if ($includeDeleted) {
            $sql .= ' AND deleted_at IS NULL';
        }
        $result = $this->db->fetchAssoc($sql, $paramValues);
        return intval($result['amount']);
    }

    public function fetchReferences($entity) {
        if (!$entity) {
            return;
        }
        foreach ($this->definition->getFieldNames() as $field) {
            if ($this->definition->getType($field) !== 'reference') {
                continue;
            }
            $nameField = $this->definition->getReferenceNameField($field);
            $sql = 'SELECT '.$nameField.' FROM ';
            $sql .= $this->definition->getReferenceTable($field).' WHERE id = ? AND deleted_at IS NULL';
            $result = $this->db->fetchAssoc($sql, array($entity->get($field)));
            if ($result) {
                $entity->set($field,
                    array('id' => $entity->get($field), 'name' => $result[$nameField]));
            }
        }
    }

}
