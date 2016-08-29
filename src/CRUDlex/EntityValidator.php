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

use \Valdi\Validator;

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
     * Builds up the validation rules for a single field according to the
     * entity definition.
     *
     * @param string $field
     * the field for the rules
     * @param AbstractData $data
     * the data instance to use for validation
     * @param Validator $validator
     * the validator to use
     *
     * @return array
     * the validation rules for the field
     */
    protected function fieldToRules($field, AbstractData $data, Validator $validator) {
        $setItems = $this->definition->getSetItems($field);
        $rulesMapping = [
            'boolean' => ['boolean'],
            'float' => ['floating'],
            'integer' => ['integer'],
            'date' => ['dateTime', 'Y-m-d'],
            'datetime' => ['or', $validator, ['dateTime', 'Y-m-d H:i'], ['dateTime', 'Y-m-d H:i:s']],
            'set' => array_merge(['inSet'], $setItems),
            'reference' => ['reference', $data, $field]
        ];
        $type         = $this->definition->getType($field);
        $rules        = [];
        if (array_key_exists($type, $rulesMapping)) {
            $rules[] = $rulesMapping[$type];
        }
        if ($this->definition->isRequired($field)) {
            $rules[] = ['required'];
        }
        if ($this->definition->isUnique($field)) {
            $rules[] = ['unique', $data, $this->entity, $field];
        }
        return $rules;
    }

    /**
     * Builds up the validation rules for the entity according to its
     * definition.
     * @param AbstractData $data
     * the data instance to use for validation
     * @param Validator $validator
     * the validator to use
     *
     * @return array
     * the validation rules for the entity
     */
    protected function buildUpRules(AbstractData $data, Validator $validator) {
        $fields = $this->definition->getEditableFieldNames();
        $rules = [];
        foreach ($fields as $field) {
            $fieldRules = $this->fieldToRules($field, $data, $validator);
            if (!empty($fieldRules)) {
                $rules[$field] = $fieldRules;
            }
        }
        return $rules;
    }

    /**
     * Builds up the data to validate from the entity.
     *
     * @return array
     * a map field to raw value
     */
    protected function buildUpData() {
        $data   = [];
        $fields = $this->definition->getEditableFieldNames();
        foreach ($fields as $field) {
            $data[$field] = $this->entity->getRaw($field);
            $fixed        = $this->definition->getFixedValue($field);
            if ($fixed) {
                $data[$field] = $fixed;
            }
        }
        return $data;
    }

    /**
     * Constructor.
     *
     * @param Entity $entity
     * the entity to validate
     */
    public function __construct(Entity $entity) {
        $this->entity     = $entity;
        $this->definition = $entity->getDefinition();
    }

    /**
     * Validates the entity against the definition.
     *
     * @param AbstractData $data
     * the data access instance used for counting things
     * @param integer $expectedVersion
     * the version to perform the optimistic locking check on
     *
     * @return array
     * an array with the fields "valid" and "errors"; valid provides a quick
     * check whether the given entity passes the validation and errors is an
     * array with all errored fields as keys and arrays as values; this field arrays
     * contains the actual errors on the field: "boolean", "floating", "integer",
     * "dateTime" (for dates and datetime fields), "inSet", "reference", "required",
     * "unique", "value" (only for the version field, set if the optimistic locking
     * failed).
     */
    public function validate(AbstractData $data, $expectedVersion) {
        $validator = new Validator();
        $validator->addValidator('unique', new UniqueValidator());
        $validator->addValidator('reference', new ReferenceValidator());
        $rules                 = $this->buildUpRules($data, $validator);
        $toValidate            = $this->buildUpData();
        $rules['version']      = [['value', $expectedVersion]];
        $toValidate['version'] = $this->entity->get('version');
        $validation            = $validator->isValid($rules, $toValidate);
        return $validation;
    }

}
