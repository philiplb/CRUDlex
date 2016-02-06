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

use Symfony\Component\HttpFoundation\Request;
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
     * Converts a given value to the given type.
     *
     * @param mixed $value
     * the value to convert
     * @param string $type
     * the type to convert to like 'int' or 'float'
     *
     * @return mixed
     * the converted value
     */
    protected function toType($value, $type) {
        settype($value, $type);
        return $value;
    }

    /**
     * Validates the given field for the required constraint.
     *
     * @param string $field
     * the field to validate
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    protected function validateRequired($field, &$fieldErrors, &$valid) {
        if ($this->definition->isRequired($field) && !$this->definition->getFixedValue($field) &&
            (!array_key_exists($field, $this->entity)
            || $this->entity[$field] === null
            || $this->entity[$field] === '')) {
            $fieldErrors[$field]['required'] = true;
            $valid = false;
        }
    }

    /**
     * Validates the given field for the unique constraint.
     *
     * @param string $field
     * the field to validate
     * @param CRUDData $data
     * the data instance to work with
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    private function validateUnique($field, CRUDData $data, &$fieldErrors, &$valid) {
        if ($this->definition->isUnique($field) && array_key_exists($field, $this->entity) && $this->entity[$field]) {
            $params = array($field => $this->entity[$field]);
            $paramsOperators = array($field => '=');
            if ($this->entity['id'] !== null) {
                $params['id'] = $this->entity['id'];
                $paramsOperators['id'] = '!=';
            }
            $amount = intval($data->countBy($this->definition->getTable(), $params, $paramsOperators, true));
            if ($amount > 0) {
                $fieldErrors[$field]['unique'] = true;
                $valid = false;
            }
        }
    }

    /**
     * Validates the given field for the set type.
     *
     * @param string $field
     * the field to validate
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    private function validateSet($field, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        if ($type == 'set' && $this->entity[$field]) {
            $setItems = $this->definition->getSetItems($field);
            if (!in_array($this->entity[$field], $setItems)) {
                $fieldErrors[$field]['input'] = true;
                $valid = false;
            }
        }
    }

    /**
     * Validates the given field for a number type.
     *
     * @param string $field
     * the field to validate
     * @param string $numberType
     * the type, might be 'int' or 'float'
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    private function validateNumber($field, $numberType, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        if ($type == $numberType && !in_array($this->entity[$field], array('', null), true) && (string)$this->toType($this->entity[$field], $numberType) != $this->entity[$field]) {
            $fieldErrors[$field]['input'] = true;
            $valid = false;
        }
    }

    /**
     * Validates the given field for the date type.
     *
     * @param string $field
     * the field to validate
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    private function validateDate($field, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        if ($type == 'date' && $this->entity[$field] && \DateTime::createFromFormat('Y-m-d', $this->entity[$field]) === false) {
            $fieldErrors[$field]['input'] = true;
            $valid = false;
        }
    }

    /**
     * Validates the given field for the datetime type.
     *
     * @param string $field
     * the field to validate
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    private function validateDateTime($field, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        if ($type == 'datetime' && $this->entity[$field] &&
            \DateTime::createFromFormat('Y-m-d H:i', $this->entity[$field]) === false &&
            \DateTime::createFromFormat('Y-m-d H:i:s', $this->entity[$field]) === false) {
            $fieldErrors[$field]['input'] = true;
            $valid = false;
        }
    }

    /**
     * Validates the given field for the reference type.
     *
     * @param string $field
     * the field to validate
     * @param CRUDData $data
     * the data instance to work with
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    private function validateReference($field, CRUDData $data, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        if ($type == 'reference' && $this->entity[$field] !== '' && $this->entity[$field] !== null) {
            $params = array('id' => $this->entity[$field]);
            $paramsOperators = array('id' => '=');
            $amount = $data->countBy($this->definition->getReferenceTable($field), $params, $paramsOperators, false);
            if ($amount == 0) {
                $fieldErrors[$field]['input'] = true;
                $valid = false;
            }
        }
    }

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
        $type = $this->definition->getType($field);
        switch ($type) {
            case 'int':
            case 'float':
                $value = $value !== '' && $value !== null ? $this->toType($value, $type) : null;
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
     * an array with the fields "valid" and "fields"; valid provides a quick
     * check whether the given entity passes the validation and fields is an
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
        $fieldErrors = array();
        $valid = true;
        foreach ($fields as $field) {
            $fieldErrors[$field] = array('required' => false, 'unique' => false, 'input' => false);

            $this->validateRequired($field, $fieldErrors, $valid);
            $this->validateUnique($field, $data, $fieldErrors, $valid);

            $this->validateSet($field, $fieldErrors, $valid);
            $this->validateNumber($field, 'int', $fieldErrors, $valid);
            $this->validateNumber($field, 'float', $fieldErrors, $valid);
            $this->validateDate($field, $fieldErrors, $valid);
            $this->validateDateTime($field, $fieldErrors, $valid);
            $this->validateReference($field, $data, $fieldErrors, $valid);

        }
        return array('valid' => $valid, 'fields' => $fieldErrors);
    }

    /**
     * Populates the entities fields from the requests parameters.
     *
     * @param Request $request
     * the request to take the field data from
     */
    public function populateViaRequest(Request $request) {
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            if ($this->definition->getType($field) == 'file') {
                $file = $request->files->get($field);
                if ($file) {
                    $this->set($field, $file->getClientOriginalName());
                }
            } else {
                $this->set($field, $request->get($field));
            }
        }
    }

}
