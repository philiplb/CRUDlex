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

use RomaricDrigon\MetaYaml\MetaYaml;
use RomaricDrigon\MetaYaml\Loader\YamlLoader;

/**
 * An entity definition validator using the romaricdrigon/MetaYaml validator with the
 * given definitionSchema.yml.
 */
class EntityDefinitionValidator implements EntityDefinitionValidatorInterface
{

    /**
     * {@inheritdoc}
     */
    public function validate(array $data) {
        $loader = new YamlLoader();
        $schemaContent = $loader->loadFromFile(__DIR__.'/../definitionSchema.yml');
        $schema = new MetaYaml($schemaContent);
        try {
            $schema->validate($data);
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
