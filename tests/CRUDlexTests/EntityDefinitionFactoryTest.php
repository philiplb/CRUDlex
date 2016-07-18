<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlexTests;

use CRUDlex\EntityDefinition;
use CRUDlex\ServiceProvider;
use CRUDlex\EntityDefinitionFactory;

class EntityDefinitionFactoryTest extends \PHPUnit_Framework_TestCase {

    public function testCreateEntityDefinition() {
        $crudServiceProvider = new ServiceProvider();
        $entityDefinitionFactory = new EntityDefinitionFactory();
        $instance = $entityDefinitionFactory->createEntityDefinition('', array(), '', array(), array(), $crudServiceProvider);
        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof EntityDefinition);
    }

}