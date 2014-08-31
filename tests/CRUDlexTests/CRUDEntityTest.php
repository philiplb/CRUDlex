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

use CRUDlexTestEnv\CRUDTestDBSetup;
use CRUDlex\CRUDEntity;

class CRUDEntityTest extends \PHPUnit_Framework_TestCase {

    protected $crudServiceProvider;

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $this->crudServiceProvider = CRUDTestDBSetup::createCRUDServiceProvider();
        $this->dataBook = $this->crudServiceProvider->getData('book');
        $this->dataLibrary = $this->crudServiceProvider->getData('library');
    }

    public function testGetSet() {
        $definition = $this->crudServiceProvider->getData('library')->getDefinition();
        $entity = new CRUDEntity($definition);
        $entity->set('test', 'testdata');
        $read = $entity->get('test');
        $expected = 'testdata';
        $this->assertSame($read, $expected);

        $entity->set('test', 'testdata2');
        $read = $entity->get('test');
        $expected = 'testdata2';
        $this->assertSame($read, $expected);

        $read = $entity->get('testNull');
        $this->assertNull($read);
    }

}
