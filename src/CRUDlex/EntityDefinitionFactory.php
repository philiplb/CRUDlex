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
 * Default implementation of the EntiyDefinitionFactoryInterface being used if the key "crud.entitydefinitionfactory" is
 * not given during the registration of the ServiceProvider.
 */
class EntityDefinitionFactory implements EntityDefinitionFactoryInterface {

    /**
     * {@inheritdoc}
     */
    public function createEntityDefinition($table, array $fields, $label, $localeLabels, array $standardFieldLabels, ServiceProvider $serviceProvider) {
        return new EntityDefinition($table, $fields, $label, $localeLabels, $standardFieldLabels, $serviceProvider);
    }
}
