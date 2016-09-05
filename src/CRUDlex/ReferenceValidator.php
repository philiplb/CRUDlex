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
 * A validator to check references.
 */
class ReferenceValidator implements ValidatorInterface {

    /**
     * {@inheritdoc}
     */
    public function isValid($value, array $parameters) {

        if (in_array($value, [null, ''])) {
            return true;
        }

        $data            = $parameters[0];
        $field           = $parameters[1];
        $definition      = $data->getDefinition();
        $params          = ['id' => $value];
        $paramsOperators = ['id' => '='];
        $referenceEntity = $definition->getReferenceEntity($field);
        $table           = $definition->getServiceProvider()->getData($referenceEntity)->getDefinition()->getTable();
        $amount          = $data->countBy($table, $params, $paramsOperators, false);
        return $amount > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvalidDetails() {
        return 'reference';
    }

}
