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
            'library',
            'cover',
            'price'
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

    public function testSetType() {
        $read = $this->definitionLibrary->setType('name', 'multiline');
        $read = $this->definitionLibrary->getType('name');
        $expected = 'multiline';
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
            'library',
            'cover',
            'price'
        );
        $this->assertSame($read, $expected);
    }

    public function testGetListFieldNames() {
        $read = $this->definition->getListFieldNames();
        $expected = array(
            'author',
            'title',
            'library'
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
            'isOpenOnSundays',
            'planet'
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

    public function testAddAndGetChild() {
        $this->definition->addChild('foo', 'bar', 'bla');
        $read = $this->definition->getChildren();
        $expected = array(
            array('foo', 'bar', 'bla')
        );
        $this->assertSame($read, $expected);
    }

    public function testGetFilePath() {
        $read = $this->definition->getFilePath('cover');
        $expected = 'tests/uploads';
        $this->assertSame($read, $expected);
        $read = $this->definition->getFilePath('title');
        $this->assertNull($read);
        $read = $this->definition->getFilePath('foo');
        $this->assertNull($read);
        $read = $this->definition->getFilePath(null);
        $this->assertNull($read);
    }

    public function testGetFixedValue() {
        $read = $this->definitionLibrary->getFixedValue('planet');
        $expected = 'Earth';
        $this->assertSame($read, $expected);
        $read = $this->definitionLibrary->getFixedValue('title');
        $this->assertNull($read);
        $read = $this->definitionLibrary->getFixedValue('foo');
        $this->assertNull($read);
        $read = $this->definitionLibrary->getFixedValue(null);
        $this->assertNull($read);
    }

    public function testSetFixedValue() {
        $this->definitionLibrary->setFixedValue('planet', 'Mars');
        $read = $this->definitionLibrary->getFixedValue('planet');
        $expected = 'Mars';
        $this->assertSame($read, $expected);
    }

    public function testSetRequired() {
        $this->definition->setRequired('cover', false);
        $read = $this->definition->isRequired('cover');
        $expected = false;
        $this->assertSame($read, $expected);
        $this->definition->setRequired('cover', true);
        $read = $this->definition->isRequired('cover');
        $expected = true;
        $this->assertSame($read, $expected);
        $this->definition->setRequired('foo', true);
        $read = $this->definition->isRequired('foo');
        $expected = true;
        $this->assertSame($read, $expected);
    }

    public function testChildrenLabelFields() {
        $read = $this->definitionLibrary->getChildrenLabelFields();
        $expected = array('book' => 'title');
        $this->assertSame($read, $expected);
    }

    public function testGetFloatStep() {
        $read = $this->definition->getFloatStep('price');
        $expected = 0.1;
        $this->assertSame($read, $expected);
        $read = $this->definition->getFloatStep('title');
        $this->assertNull($read);
        $read = $this->definition->getFloatStep('foo');
        $this->assertNull($read);
        $read = $this->definition->getFloatStep(null);
        $this->assertNull($read);
    }

}
