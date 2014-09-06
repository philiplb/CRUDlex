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
use CRUDlex\CRUDServiceProvider;
use CRUDlex\CRUDEntity;

class CRUDEntityDefinitionTest extends \PHPUnit_Framework_TestCase {

    protected $definition;

    protected $definitionLibrary;

    protected function setUp() {
        $crudServiceProvider = CRUDTestDBSetup::createCRUDServiceProvider();
        $this->definition = $crudServiceProvider->getData('book')->getDefinition();
        $this->definitionLibrary = $crudServiceProvider->getData('library')->getDefinition();
    }

    public function testGetFieldNames() {
        $read = $this->definition->getFieldNames();
        $expected = array(
            'id',
            'created_at',
            'updated_at',
            'version',
            'deleted_at',
            'title',
            'author',
            'pages',
            'release',
            'library'
        );
        $this->assertSame($read, $expected);
    }

    public function testGetType() {
        $fields = array('title', 'pages', 'release', 'library');
        $expected = array('text', 'int', 'date', 'reference', null);
        $read = array();
        foreach ($fields as $field) {
            $read[] = $this->definition->getType($field);
        }
        $read[] = $this->definition->getType('foo');
        $this->assertSame($read, $expected);
    }

    public function testGetPublicFieldNames() {
        $read = $this->definition->getPublicFieldNames();
        $expected = array(
            'id',
            'created_at',
            'updated_at',
            'title',
            'author',
            'pages',
            'release',
            'library'
        );
        $this->assertSame($read, $expected);
    }

    public function testGetListFieldNames() {
        $read = $this->definition->getListFieldNames();
        $expected = array(
            'author',
            'title',
            'library',
        );
        $this->assertSame($read, $expected);
        $read = $this->definitionLibrary->getListFieldNames();
        $expected = array(
            'id',
            'created_at',
            'updated_at',
            'name',
            'type',
            'opening',
            'isOpenOnSundays'
        );
        $this->assertSame($read, $expected);
    }

    public function testIsRequired() {
        $read = $this->definition->isRequired('title');
        $this->assertTrue($read);
        $read = $this->definition->isRequired('release');
        $this->assertFalse($read);
        $read = $this->definition->isRequired('false');
        $this->assertFalse($read);
        $read = $this->definition->isRequired(null);
        $this->assertFalse($read);
    }

    public function testGetReferenceTable() {
        $read = $this->definition->getReferenceTable('library');
        $expected = 'library';
        $this->assertSame($read, $expected);
        $read = $this->definition->getReferenceTable('title');
        $this->assertNull($read);
        $read = $this->definition->getReferenceTable('foo');
        $this->assertNull($read);
        $read = $this->definition->getReferenceTable(null);
        $this->assertNull($read);
    }

    public function testGetReferenceNameField() {
        $read = $this->definition->getReferenceNameField('library');
        $expected = 'name';
        $this->assertSame($read, $expected);
        $read = $this->definition->getReferenceNameField('title');
        $this->assertNull($read);
        $read = $this->definition->getReferenceNameField('foo');
        $this->assertNull($read);
        $read = $this->definition->getReferenceNameField(null);
        $this->assertNull($read);
    }

    public function testGetReferenceEntity() {
        $read = $this->definition->getReferenceEntity('library');
        $expected = 'library';
        $this->assertSame($read, $expected);
        $read = $this->definition->getReferenceEntity('title');
        $this->assertNull($read);
        $read = $this->definition->getReferenceEntity('foo');
        $this->assertNull($read);
        $read = $this->definition->getReferenceEntity(null);
        $this->assertNull($read);
    }

    public function testGetSetItems() {
        $read = $this->definitionLibrary->getSetItems('type');
        $expected = array('small', 'medium', 'large');
        $this->assertSame($read, $expected);

        $read = $this->definitionLibrary->getSetItems('name');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSetItems('foo');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSetItems(null);
        $this->assertNull($read);
    }

    public function testIsUnique() {
        $read = $this->definitionLibrary->isUnique('name');
        $this->assertTrue($read);
        $read = $this->definition->isUnique('release');
        $this->assertFalse($read);
        $read = $this->definition->isUnique('false');
        $this->assertFalse($read);
        $read = $this->definition->isUnique(null);
        $this->assertFalse($read);
    }

    public function testGetFieldLabel() {
        $read = $this->definition->getFieldLabel('library');
        $expected = 'Library';
        $this->assertSame($read, $expected);
        $read = $this->definition->getFieldLabel('id');
        $expected = 'Id';
        $this->assertSame($read, $expected);
        $read = $this->definition->getFieldLabel('foo');
        $expected = 'foo';
        $this->assertSame($read, $expected);
        $read = $this->definition->getFieldLabel(null);
        $this->assertNull($read);
    }

    public function testGetTable() {
        $read = $this->definition->getTable();
        $expected = 'book';
        $this->assertSame($read, $expected);
    }

    public function testGetLabel() {
        $read = $this->definition->getLabel();
        $expected = 'Book';
        $this->assertSame($read, $expected);
    }

    public function testAddAndGetParent() {
        $this->definition->addParent('foo', 'bar');
        $read = $this->definition->getParents();
        $expected = array(
            array('foo', 'bar')
        );
        $this->assertSame($read, $expected);
    }

}
