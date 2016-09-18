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

use \Valdi\Validator\ValidatorInterface;

/**
 * A validator to check for an unique field.
 */
class UniqueValidator implements ValidatorInterface {

    /**
     * Checks whether the unique constraint is valid for a many-to-many field.
     *
     * @param array $value
     * the value to check
     * @param AbstractData $data
     * the data to perform the check with
     * @param Entity $entity
     * the entity to perform the check on
     * @param $field
     * the many field to perform the check on
     *
     * @return boolean
     * true if it is a valid unique many-to-many constraint
     */
    protected function isValidUniqueMany(array $value, AbstractData $data, Entity $entity, $field) {
        return !$data->hasManySet($field, array_column($value, 'id'), $entity->get('id'));
    }

    /**
     * Performs the regular unique validation.
     *
     * @param $value
     * the value to validate
     * @param $data
     * the data instance to validate with
     * @param $entity
     * the entity of the field
     * @param $field
     * the field to validate
     *
     * @return boolean
     * true if everything is valid
     */
    protected function isValidUnique($value, AbstractData $data, Entity $entity, $field)
    {
        $params          = [$field => $value];
        $paramsOperators = [$field => '='];
        if ($entity->get('id') !== null) {
            $params['id']          = $entity->get('id');
            $paramsOperators['id'] = '!=';
        }
        $amount = intval($data->countBy($data->getDefinition()->getTable(), $params, $paramsOperators, true));
        return $amount == 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($value, array $parameters) {

        if (in_array($value, [null, ''])) {
            return true;
        }

        $data   = $parameters[0];
        $entity = $parameters[1];
        $field  = $parameters[2];
        $type   = $data->getDefinition()->getType($field);

        if ($type === 'many') {
            return $this->isValidUniqueMany($value, $data, $entity, $field);
        }

        return $this->isValidUnique($value, $data, $entity, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvalidDetails() {
        return 'unique';
    }

}
