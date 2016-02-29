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

use CRUDlex\Data;

/**
 * Performs validation of the field values of the given {@see Entity}.
 */
class EntityValidator {

    /**
     * The entity to validate.
     */
    protected $entity;

    /**
     * The entities definition.
     */
    protected $definition;

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
        if ($this->definition->isRequired($field)
            && !$this->definition->getFixedValue($field)
            && in_array($this->entity->getRaw($field), array(null, ''), true)) {
            $fieldErrors[$field]['required'] = true;
            $valid = false;
        }
    }

    /**
     * Validates the given field for the unique constraint.
     *
     * @param string $field
     * the field to validate
     * @param Data $data
     * the data instance to work with
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    protected function validateUnique($field, Data $data, &$fieldErrors, &$valid) {
        $value = $this->entity->getRaw($field);
        if ($this->definition->isUnique($field) && $value) {
            $params = array($field => $value);
            $paramsOperators = array($field => '=');
            if ($this->entity->get('id') !== null) {
                $params['id'] = $this->entity->get('id');
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
    protected function validateSet($field, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        $value = $this->entity->getRaw($field);
        if ($type == 'set' && $value) {
            $setItems = $this->definition->getSetItems($field);
            if (!in_array($value, $setItems)) {
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
     * @param string $expectedType
     * the expected CRUDlex type, might be 'integer' or 'float'
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    protected function validateNumber($field, $numberType, $expectedType, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        $value = $this->entity->getRaw($field);
        $casted = $value;
        settype($casted, $numberType);
        if ($type == $expectedType
            && !in_array($value, array('', null), true)
            && (string)$casted != $value) {
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
    protected function validateDate($field, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        $value = $this->entity->getRaw($field);
        if ($type == 'date' && $value
            && \DateTime::createFromFormat('Y-m-d', $value) === false) {
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
    protected function validateDateTime($field, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        $value = $this->entity->getRaw($field);
        if ($type == 'datetime' && $value &&
            \DateTime::createFromFormat('Y-m-d H:i', $value) === false &&
            \DateTime::createFromFormat('Y-m-d H:i:s', $value) === false) {
            $fieldErrors[$field]['input'] = true;
            $valid = false;
        }
    }

    /**
     * Validates the given field for the reference type.
     *
     * @param string $field
     * the field to validate
     * @param Data $data
     * the data instance to work with
     * @param array &$fieldErrors
     * the error collecting array
     * @param boolean &$valid
     * the validation flag
     */
    protected function validateReference($field, Data $data, &$fieldErrors, &$valid) {
        $type = $this->definition->getType($field);
        $value = $this->entity->getRaw($field);
        if ($type == 'reference' && $value !== '' && $value !== null) {
            $params = array('id' => $value);
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
     * @param Entity $entity
     * the entity to validate
     */
    public function __construct(Entity $entity) {
        $this->entity = $entity;
        $this->definition = $entity->getDefinition();
    }

    /**
     * Validates the entity against the definition.
     *
     * @param Data $data
     * the data access instance used for counting things
     * @param integer $expectedVersion
     * the version to perform the optimistic locking check on
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
    public function validate(Data $data, $expectedVersion) {

        $fields = $this->definition->getEditableFieldNames();
        $fieldErrors = array();
        $valid = true;
        $optimisticLocking = false;

        if ($this->entity->get('id') && $expectedVersion !== $this->entity->get('version')) {
            $valid = false;
            $optimisticLocking = true;
        }

        foreach ($fields as $field) {
            $fieldErrors[$field] = array('required' => false, 'unique' => false, 'input' => false);

            $this->validateRequired($field, $fieldErrors, $valid);
            $this->validateUnique($field, $data, $fieldErrors, $valid);

            $this->validateSet($field, $fieldErrors, $valid);
            $this->validateNumber($field, 'int', 'integer', $fieldErrors, $valid);
            $this->validateNumber($field, 'float', 'float', $fieldErrors, $valid);
            $this->validateDate($field, $fieldErrors, $valid);
            $this->validateDateTime($field, $fieldErrors, $valid);
            $this->validateReference($field, $data, $fieldErrors, $valid);

        }
        return array(
            'valid' => $valid,
            'optimisticLocking' => $optimisticLocking,
            'fields' => $fieldErrors
        );
    }

}
