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
use CRUDlex\EntityDefinition;

/**
 * Represents a single set of data in field value pairs like the row in a
 * database. Depends of course on the {@see AbstractData} implementation being used.
 * With this objects, the data is passed arround and validated.
 */
class Entity {

    /**
     * The {@see EntityDefinition} defining how this entity looks like.
     */
    protected $definition;

    /**
     * Holds the key value data of the entity.
     */
    protected $entity;


    /**
     * Converts a given value to the given type.
     *
     * @param mixed $value
     * the value to convert
     * @param string $type
     * the type to convert to like 'integer' or 'float'
     *
     * @return mixed
     * the converted value
     */
    protected function toType($value, $type) {
        if (in_array($type, array('integer', 'float')) && $value !== '' && $value !== null) {
            settype($value, $type == 'integer' ? 'int' : 'float');
        } else if ($type == 'boolean') {
            $value = $value && $value !== '0';
        } else if ($type == 'reference') {
            $value = $value !== '' ? $value : null;
        }
        return $value;
    }

    /**
     * Constructor.
     *
     * @param EntityDefinition $definition
     * the definition how this entity looks
     */
    public function __construct(EntityDefinition $definition) {
        $this->definition = $definition;
        $this->entity     = array();
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
     * Gets the raw value of a field no matter what type it is.
     * This is usefull for input validation for example.
     *
     * @param string $field
     * the field
     *
     * @return mixed
     * null on invalid field or else the raw value
     */
    public function getRaw($field) {
        if (!array_key_exists($field, $this->entity)) {
            return null;
        }
        return $this->entity[$field];
    }

    /**
     * Gets the value of a field in its specific type.
     *
     * @param string $field
     * the field
     *
     * @return mixed
     * null on invalid field, an integer if the definition says that the
     * type of the field is an integer, a boolean if the field is a boolean or
     * else the raw value
     */
    public function get($field) {

        if ($this->definition->getFixedValue($field) !== null) {
            return $this->definition->getFixedValue($field);
        }

        if (!array_key_exists($field, $this->entity)) {
            return null;
        }

        $type  = $this->definition->getType($field);
        $value = $this->toType($this->entity[$field], $type);
        return $value;
    }

    /**
     * Gets the entity definition.
     *
     * @return EntityDefinition
     * the definition
     */
    public function getDefinition() {
        return $this->definition;
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
