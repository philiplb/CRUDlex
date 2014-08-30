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
class CRUDEntityDefinition {

    protected $table;

    protected $fields;

    protected $label;

    protected $parents;

    protected $standardFieldLabels;

    protected function getFilteredFieldNames($exclude) {
        $fieldNames = $this->getFieldNames();
        $result = array();
        foreach ($fieldNames as $fieldName) {
            if (!in_array($fieldName, $exclude)) {
                $result[] = $fieldName;
            }
        }
        return $result;
    }

    protected function getFieldValue($name, $key) {
        if (key_exists($name, $this->fields) && key_exists($key, $this->fields[$name])) {
            return $this->fields[$name][$key];
        }
        return null;
    }

    protected function getReferenceValue($fieldName, $key) {
        if ($this->getType($fieldName) != 'reference') {
            return null;
        }
        return $this->fields[$fieldName]['reference'][$key];
    }

    /**
     *
     * @param type $db the Doctrine DB connection
     * @param type $table the table to use
     * @param type $fields map with key = fieldname, value = map with keys
     *  type = text, date, int, reference,
     *  reference = only if type is reference, then a map with keys "table", "nameField", "entity",
     *  required = true or false),
     *  unique = true or false)
     *  The field id is always expected. So are updated_at, created_at,
     *  deleted_at and version.
     */
    public function __construct($table, $fields, $label, $standardFieldLabels) {
        $this->table = $table;
        $this->fields = $fields;
        $this->parents = array();
        $this->label = $label;
        $this->standardFieldLabels = $standardFieldLabels;
    }

    public function getFieldNames() {
        $fieldNames = $this->getReadOnlyFields();
        foreach ($this->fields as $field => $value) {
            $fieldNames[] = $field;
        }
        return $fieldNames;
    }

    public function getPublicFieldNames() {
        $exclude = array('version', 'deleted_at');
        $result = $this->getFilteredFieldNames($exclude);
        return $result;
    }

    public function getEditableFieldNames() {
        $result = $this->getFilteredFieldNames($this->getReadOnlyFields());
        return $result;
    }

    public function getReadOnlyFields() {
        return array('id', 'created_at', 'updated_at', 'version', 'deleted_at');
    }

    public function getType($fieldName) {
        return $this->getFieldValue($fieldName, 'type');
    }

    public function isRequired($fieldName) {
        $result = $this->getFieldValue($fieldName, 'required');
        if ($result === null) {
            $result = false;
        }
        return $result;
    }

    public function getReferenceTable($fieldName) {
        return $this->getReferenceValue($fieldName, 'table');
    }

    public function getReferenceNameField($fieldName) {
        return $this->getReferenceValue($fieldName, 'nameField');
    }

    public function getReferenceEntity($fieldName) {
        return $this->getReferenceValue($fieldName, 'entity');
    }

    public function isUnique($fieldName) {
        $result = $this->getFieldValue($fieldName, 'unique');
        if ($result === null) {
            $result = false;
        }
        return $result;
    }

    public function getFieldLabel($fieldName) {
        $result = $this->getFieldValue($fieldName, 'label');
        if ($result === null && key_exists($fieldName, $this->standardFieldLabels)) {
            $result = $this->standardFieldLabels[$fieldName];
        }
        if ($result === null) {
            $result = $fieldName;
        }
        return $result;
    }

    public function getTable() {
        return $this->table;
    }

    public function getLabel() {
        return $this->label;
    }

    public function addParent($entity, $fieldName) {
        $this->parents[] = array($entity, $fieldName);
    }

    public function getParents() {
        return $this->parents;
    }
}
