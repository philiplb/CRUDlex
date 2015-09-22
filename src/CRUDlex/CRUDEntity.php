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
use CRUDlex\CRUDData;

/**
 * Represents a single set of data in field value pairs like the row in a
 * database. Depends of course on the {@see CRUDData} implementation being used.
 * With this objects, the data is passed arround and validated.
 */
class CRUDEntity {

    /**
     * The {@see CRUDEntityDefinition} defining how this entity looks like.
     */
    protected $definition;

    /**
     * Holds the key value data of the entity.
     */
    protected $entity = array();

    /**
     * Constructor.
     *
     * @param CRUDEntityDefinition $definition
     * the definition how this entity looks
     */
    public function __construct(CRUDEntityDefinition $definition) {
        $this->definition = $definition;
    }

    /**
     * Sets a field value pair of this entity.
     *
     * @param string $field
     * the field
     * @param mixed $value
     * the value
     */
    public function set($field, $value) {
        $this->entity[$field] = $value;
    }

    /**
     * Gets the value of a field.
     *
     * @param string $field
     * the field
     *
     * @return mixed
     * null on invalid field, an int if the definition says that the
     * type of the field is an int, a boolean if the field is a bool or
     * else the raw value
     */
    public function get($field) {

        if ($this->definition->getFixedValue($field) !== null) {
            return $this->definition->getFixedValue($field);
        }

        if (!array_key_exists($field, $this->entity)) {
            return null;
        }
        $value = $this->entity[$field];

        switch ($this->definition->getType($field)) {
            case 'int':
                $value = $value !== '' && $value !== null ? intval($value) : null;
                break;
            case 'float':
                $value = $value !== '' && $value !== null ? floatval($value) : null;
                break;
            case 'bool':
                $value = $value && $value !== '0';
                break;
        }
        return $value;
    }

    /**
     * Gets the entity definition.
     *
     * @return CRUDEntityDefinition
     * the definition
     */
    public function getDefinition() {
        return $this->definition;
    }

    /**
     * Validates the entity against the definition.
     *
     * @param CRUDData $data
     * the data access instance used for counting things
     *
     * @return array
     * an array with the fields "valid" and "errors"; valid provides a quick
     * check whether the given entity passes the validation and errors is an
     * array with all fields as keys and arrays as values; this field arrays
     * contain three keys: required, unique and input; each of them represents
     * with a boolean whether the input is ok in that way; if "required" is
     * true, the field wasn't set, unique means the uniqueness of the field in
     * the datasource and input is used to indicate whether the form of the
     * value is correct (a valid int, date, depending on the type in the
     * definition)
     */
    public function validate(CRUDData $data) {

        $fields = $this->definition->getEditableFieldNames();
        $errors = array();
        $valid = true;
        foreach ($fields as $field) {
            $errors[$field] = array('required' => false, 'unique' => false, 'input' => false);

            // Check for required
            if ($this->definition->isRequired($field) && !$this->definition->getFixedValue($field) &&
                (!array_key_exists($field, $this->entity)
                || $this->entity[$field] === null
                || $this->entity[$field] === '')) {
                $errors[$field]['required'] = true;
                $valid = false;
            }

            // Check for uniqueness
            if ($this->definition->isUnique($field) && array_key_exists($field, $this->entity) && $this->entity[$field]) {
                $params = array($field => $this->entity[$field]);
                $paramsOperators = array($field => '=');
                if ($this->entity['id'] !== null) {
                    $params['id'] = $this->entity['id'];
                    $paramsOperators['id'] = '!=';
                }
                $amount = intval($data->countBy($this->definition->getTable(), $params, $paramsOperators, true));
                if ($amount > 0) {
                    $errors[$field]['unique'] = true;
                    $valid = false;
                }
            }

            // Check for set type
            $type = $this->definition->getType($field);
            if ($type == 'set' && $this->entity[$field]) {
                $setItems = $this->definition->getSetItems($field);
                if (!in_array($this->entity[$field], $setItems)) {
                    $errors[$field]['input'] = true;
                    $valid = false;
                }
            }

            // Check for int type
            $type = $this->definition->getType($field);
            if ($type == 'int' && $this->entity[$field] !== '' && $this->entity[$field] !== null && (string)(int)$this->entity[$field] != $this->entity[$field]) {
                $errors[$field]['input'] = true;
                $valid = false;
            }

            // Check for float type
            $type = $this->definition->getType($field);
            if ($type == 'float' && $this->entity[$field] !== '' && $this->entity[$field] !== null && (string)(float)$this->entity[$field] != $this->entity[$field]) {
                $errors[$field]['input'] = true;
                $valid = false;
            }

            // Check for date type
            if ($type == 'date' && $this->entity[$field] && \DateTime::createFromFormat('Y-m-d', $this->entity[$field]) === false) {
                $errors[$field]['input'] = true;
                $valid = false;
            }

            // Check for datetime type
            if ($type == 'datetime' && $this->entity[$field] &&
                \DateTime::createFromFormat('Y-m-d H:i', $this->entity[$field]) === false &&
                \DateTime::createFromFormat('Y-m-d H:i:s', $this->entity[$field]) === false) {
                $errors[$field]['input'] = true;
                $valid = false;
            }

            // Check for reference type
            if ($type == 'reference' && $this->entity[$field] !== '' && $this->entity[$field] !== null) {
                $params = array('id' => $this->entity[$field]);
                $paramsOperators = array('id' => '=');
                $amount = $data->countBy($this->definition->getReferenceTable($field), $params, $paramsOperators, false);
                if ($amount == 0) {
                    $errors[$field]['input'] = true;
                    $valid = false;
                }
            }
        }
        return array('valid' => $valid, 'errors' => $errors);
    }

}
