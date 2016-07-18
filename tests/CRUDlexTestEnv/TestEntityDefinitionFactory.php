<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlexTestEnv;

use CRUDlex\EntityDefinition;
use CRUDlex\EntityDefinitionFactoryInterface;
use CRUDlex\ServiceProvider;

class TestEntityDefinitionFactory implements EntityDefinitionFactoryInterface {

    private $createHasBeenCalled;

    public function __construct() {
        $this->createHasBeenCalled = false;
    }

    public function createEntityDefinition($table, array $fields, $label, $localeLabels, array $standardFieldLabels, ServiceProvider $serviceProvider) {
        $this->createHasBeenCalled = true;
        return new EntityDefinition($table, $fields, $label, $localeLabels, $standardFieldLabels, $serviceProvider);
    }

    public function getCreateHasBeenCalled() {
        return $this->createHasBeenCalled;
    }
}
