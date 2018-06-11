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
use CRUDlex\Silex\ServiceProvider;
use CRUDlex\EntityDefinitionFactory;
use CRUDlexTestEnv\TestDBSetup;

class EntityDefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateEntityDefinition()
    {
        $crudService = TestDBSetup::createService();
        $entityDefinitionFactory = new EntityDefinitionFactory();
        $instance = $entityDefinitionFactory->createEntityDefinition('', [], '', [], [], $crudService);
        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof EntityDefinition);
    }

}