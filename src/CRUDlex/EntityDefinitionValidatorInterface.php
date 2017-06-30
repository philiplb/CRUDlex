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

/**
 * An interface for validating entity definitions.
 */
interface EntityDefinitionValidatorInterface
{

    /**
     * Validates the given entity definition data which was parsed from the crud.yml.
     *
     * @param array $data
     * the data to validate
     *
     * @return void
     *
     * @throws \LogicException
     * thrown if the validation failed
     */
    public function validate(array $data);

}
