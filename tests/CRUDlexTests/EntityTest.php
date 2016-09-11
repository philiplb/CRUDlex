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

use CRUDlexTestEnv\TestDBSetup;
use CRUDlex\Entity;

class EntityTest extends \PHPUnit_Framework_TestCase {

    protected $crudServiceProvider;

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $this->crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->dataBook = $this->crudServiceProvider->getData('book');
        $this->dataLibrary = $this->crudServiceProvider->getData('library');
    }

    public function testGetSet() {
        $definition = $this->crudServiceProvider->getData('book')->getDefinition();
        $entity = new Entity($definition);
        $entity->set('test', 'testdata');
        $read = $entity->get('test');
        $expected = 'testdata';
        $this->assertSame($expected, $read);

        $entity->set('test', 'testdata2');
        $read = $entity->get('test');
        $expected = 'testdata2';
        $this->assertSame($expected, $read);

        $read = $entity->get('testNull');
        $this->assertNull($read);

        $entity->set('price', 3.99);
        $read = $entity->get('price');
        $expected = 3.99;
        $this->assertSame($expected, $read);

        $entity->set('pages', 111);
        $read = $entity->get('pages');
        $expected = 111;
        $this->assertSame($expected, $read);

        // Fixed values override
        $definition->setValue('pages', 666);
        $entity->set('pages', 111);
        $read = $entity->get('pages');
        $expected = 666;
        $this->assertSame($expected, $read);


        $definition = $this->crudServiceProvider->getData('library')->getDefinition();
        $entity = new Entity($definition);

        $entity->set('isOpenOnSundays', true);
        $read = $entity->get('isOpenOnSundays');
        $expected = true;
        $this->assertSame($expected, $read);

        $entity->set('opening', '');
        $read = $entity->get('opening');
        $expected = null;
        $this->assertSame($expected, $read);

        $entity->set('opening', '2016-09-12 01:02:03');
        $read = $entity->get('opening');
        $expected = '2016-09-12 01:02:03';
        $this->assertSame($expected, $read);

    }

    public function testGetRaw() {
        $definition = $this->crudServiceProvider->getData('book')->getDefinition();
        $entity = new Entity($definition);
        $entity->set('test', 'testdata');
        $read = $entity->getRaw('test');
        $expected = 'testdata';
        $this->assertSame($read, $expected);
        $read = $entity->getRaw('test2');
        $this->assertNull($read);
    }

    public function testGetDefinition() {
        $entityLibrary = $this->dataLibrary->createEmpty();
        $read = $entityLibrary->getDefinition();
        $expected = $this->dataLibrary->getDefinition();
        $this->assertSame($read, $expected);
    }

}
