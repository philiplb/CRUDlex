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
 * Interface to make the creation of the EntityDefinitions flexible. To be handed into
 * the ServiceProvider registration via the key "crud.entitydefinitionfactory".
 */
interface EntityDefinitionFactoryInterface {

    /**
     * Creates an EntityDefinition instance.
     *
     * @param string $table
     * the table of the entity
     * @param array $fields
     * the fieldstructure just like the CRUD YAML
     * @param string $label
     * the label of the entity
     * @param array $localeLabels
     * the labels  of the entity in the locales
     * @param array $standardFieldLabels
     * labels for the fields "id", "created_at" and "updated_at"
     * @param ServiceProvider $serviceProvider
     * The current service provider
     *
     * @return EntityDefinition
     * the new instance
     */
    public function createEntityDefinition($table, array $fields, $label, $localeLabels, array $standardFieldLabels, ServiceProvider $serviceProvider);
}
