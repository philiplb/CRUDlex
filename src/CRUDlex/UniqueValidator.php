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
     * {@inheritdoc}
     */
    public function isValid($value, array $parameters) {

        if (in_array($value, array(null, ''))) {
            return true;
        }

        $data            = $parameters[0];
        $entity          = $parameters[1];
        $field           = $parameters[2];
        $params          = array($field => $value);
        $paramsOperators = array($field => '=');
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
    public function getInvalidDetails() {
        return 'unique';
    }

}
