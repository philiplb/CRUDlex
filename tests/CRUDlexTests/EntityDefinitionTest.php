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
use CRUDlex\ServiceProvider;
use CRUDlex\Entity;
use CRUDlex\EntityDefinition;

class EntityDefinitionTest extends \PHPUnit_Framework_TestCase {

    protected $definition;

    protected $definitionLibrary;

    protected function setUp() {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->definition = $crudServiceProvider->getData('book')->getDefinition();
        $this->definitionLibrary = $crudServiceProvider->getData('library')->getDefinition();
    }

    public function testGetSetFieldNames() {
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
            'secondLibrary',
            'cover',
            'price'
        );
        $this->assertSame($read, $expected);
    }

    public function testGetType() {
        $fields = array('title', 'pages', 'release', 'library',
            'id', 'created_at', 'updated_at', 'deleted_at', 'version');
        $expected = array('text', 'integer', 'date', 'reference',
            'string', 'datetime', 'datetime', 'datetime', 'integer', null);
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
            'secondLibrary',
            'cover',
            'price'
        );
        $this->assertSame($read, $expected);
    }

    public function testgetListFields() {
        $read = $this->definition->getListFields();
        $expected = array(
            'author',
            'title',
            'library'
        );
        $this->assertSame($read, $expected);
        $read = $this->definitionLibrary->getListFields();
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
        $old = $read;
        $expected = array(
            'id',
            'name'
        );
        $this->definitionLibrary->setListFields($expected);
        $read = $this->definitionLibrary->getListFields();
        $this->assertSame($read, $expected);
        $this->definitionLibrary->setListFields($old);
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

    public function testGetSetSetItems() {
        $read = $this->definitionLibrary->getSetItems('type');
        $expected = array('small', 'medium', 'large');
        $this->assertSame($read, $expected);

        $read = $this->definitionLibrary->getSetItems('name');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSetItems('foo');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSetItems(null);
        $this->assertNull($read);

        $expected = array('red', 'green', 'blue');
        $this->definitionLibrary->setSetItems('type', $expected);
        $read = $this->definitionLibrary->getSetItems('type');
        $this->assertSame($read, $expected);
    }

    public function testIsSetUnique() {
        $read = $this->definitionLibrary->isUnique('name');
        $this->assertTrue($read);
        $read = $this->definition->isUnique('release');
        $this->assertFalse($read);
        $read = $this->definition->isUnique('false');
        $this->assertFalse($read);
        $read = $this->definition->isUnique(null);
        $this->assertFalse($read);

        $this->definitionLibrary->setUnique('name', false);
        $read = $this->definitionLibrary->isUnique('name');
        $this->assertFalse($read);
    }

    public function testGetSetFieldLabel() {
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

        $expected = 'Public Library';
        $this->definition->setFieldLabel('library', $expected);
        $read = $this->definition->getFieldLabel('library');
        $this->assertSame($read, $expected);

        $this->definition->setLocale('de');
        $read = $this->definition->getFieldLabel('title');
        $expected = 'Titel';
        $this->assertSame($read, $expected);
        $this->definition->setLocale('en');
    }

    public function testGetSetTable() {
        $read = $this->definition->getTable();
        $expected = 'book';
        $this->assertSame($read, $expected);

        $expected = 'books';
        $this->definition->setTable($expected);
        $read = $this->definition->getTable();
        $this->assertSame($read, $expected);
    }

    public function testGetSetLabel() {
        $read = $this->definition->getLabel();
        $expected = 'Book';
        $this->assertSame($read, $expected);

        $expected = 'Shiny Book';
        $this->definition->setLabel($expected);
        $read = $this->definition->getLabel();
        $this->assertSame($read, $expected);

        $this->definition->setLocale('de');
        $read = $this->definition->getLabel();
        $expected = 'Bücher';
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

    public function testGetSetFilePath() {
        $read = $this->definition->getFilePath('cover');
        $expected = 'tests/uploads';
        $this->assertSame($read, $expected);
        $read = $this->definition->getFilePath('title');
        $this->assertNull($read);
        $read = $this->definition->getFilePath('foo');
        $this->assertNull($read);
        $read = $this->definition->getFilePath(null);
        $this->assertNull($read);

        $expected = 'tests/uploaded';
        $this->definition->setFilePath('cover', $expected);
        $read = $this->definition->getFilePath('cover');
        $this->assertSame($read, $expected);
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
        $this->assertTrue($read);
    }

    public function testGetSetChildrenLabelFields() {
        $read = $this->definitionLibrary->getChildrenLabelFields();
        $expected = array('book' => 'title');
        $this->assertSame($read, $expected);
        $expected = array('book' => 'author');
        $this->definitionLibrary->setChildrenLabelFields($expected);
        $read = $this->definitionLibrary->getChildrenLabelFields();
        $this->assertSame($read, $expected);
    }

    public function testGetSetFloatStep() {
        $read = $this->definition->getFloatStep('price');
        $expected = 0.1;
        $this->assertSame($read, $expected);
        $read = $this->definition->getFloatStep('title');
        $this->assertNull($read);
        $read = $this->definition->getFloatStep('foo');
        $this->assertNull($read);
        $read = $this->definition->getFloatStep(null);
        $this->assertNull($read);

        $expected = 0.2;
        $this->definition->setFloatStep('price', $expected);
        $read = $this->definition->getFloatStep('price');
        $this->assertSame($read, $expected);
    }

    public function testGetSetDescription() {
        $read = $this->definition->getDescription('author');
        $expected = 'The Author of the Book';
        $this->assertSame($read, $expected);
        $read = $this->definition->getDescription('title');
        $this->assertNull($read);
        $read = $this->definition->getDescription('foo');
        $this->assertNull($read);
        $read = $this->definition->getDescription(null);
        $this->assertNull($read);

        $expected = 'The Great Author of the Book';
        $this->definition->setDescription('author', $expected);
        $read = $this->definition->getDescription('author');
        $this->assertSame($read, $expected);
    }

    public function testIsSetDeleteCascade() {
        $this->definitionLibrary->setDeleteCascade(true);
        $read = $this->definitionLibrary->isDeleteCascade();
        $this->assertTrue($read);
        $this->definitionLibrary->setDeleteCascade(false);
        $read = $this->definitionLibrary->isDeleteCascade();
        $this->assertFalse($read);
    }

    public function testGetSetPageSize() {
        $read = $this->definition->getPageSize();
        $expected = 25;
        $this->assertSame($read, $expected);
        $this->definition->setPageSize(5);
        $read = $this->definition->getPageSize();
        $expected = 5;
        $this->assertSame($read, $expected);
    }

    public function testGetSetServiceProvider() {
        $read = $this->definition->getServiceProvider();
        $this->assertNotNull($read);

        $expected = new ServiceProvider();
        $this->definition->setServiceProvider($expected);
        $read = $this->definition->getServiceProvider();
        $this->assertSame($read, $expected);
    }

    public function testGetSetFilter() {
        $read = $this->definition->getFilter();
        $expected = array(
            'author',
            'title',
            'library'
        );
        $this->assertSame($read, $expected);
        $expected = array(
            'author',
            'title'
        );
        $this->definition->setFilter($expected);
        $read = $this->definition->getFilter();
        $this->assertSame($read, $expected);
    }

    public function testGetInvalidReferenceField() {
        $definition = new EntityDefinition(null, array('test' => array()), null, array(), array(), new ServiceProvider());
        $read = $definition->getReferenceTable('test');
        $this->assertNull($read);

        $definition = new EntityDefinition(null, array('test' => array('type' => 'reference')), null, array(), array(), new ServiceProvider());
        $read = $definition->getReferenceTable('test');
        $this->assertNull($read);

        $definition = new EntityDefinition(null, array('test' => array('type' => 'reference', 'reference' => array())), null, array(), array(), new ServiceProvider());
        $read = $definition->getReferenceTable('test');
        $this->assertNull($read);
    }

    public function testIsSetInitialSortField() {
        $read = $this->definition->getInitialSortField();
        $expected = 'created_at';
        $this->assertSame($read, $expected);
        $this->definition->setInitialSortField('author');
        $read = $this->definition->getInitialSortField();
        $expected = 'author';
        $this->assertSame($read, $expected);
    }

    public function testGetSetInitialSortAscending() {
        $read = $this->definition->isInitialSortAscending();
        $this->assertTrue($read);
        $this->definition->setInitialSortAscending(false);
        $read = $this->definition->isInitialSortAscending();
        $this->assertFalse($read);
    }

}
