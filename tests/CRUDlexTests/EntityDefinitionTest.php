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

    /** @var $definition EntityDefinition */
    protected $definition;

    /** @var $definitionLibrary EntityDefinition */
    protected $definitionLibrary;

    protected function setUp() {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->definition = $crudServiceProvider->getData('book')->getDefinition();
        $this->definitionLibrary = $crudServiceProvider->getData('library')->getDefinition();
    }

    public function testGetSetFieldNames() {
        $read = $this->definition->getFieldNames();
        $expected = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'version',
            'title',
            'author',
            'pages',
            'release',
            'library',
            'secondLibrary',
            'cover',
            'price'
        ];
        $this->assertSame($read, $expected);
    }

    public function testGetType() {
        $fields = ['title', 'pages', 'release', 'library',
            'id', 'created_at', 'updated_at', 'deleted_at', 'version'];
        $expected = ['text', 'integer', 'date', 'reference',
            'string', 'datetime', 'datetime', 'datetime', 'integer', null];
        $read = [];
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
        $expected = [
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
        ];
        $this->assertSame($read, $expected);
    }

    public function testgetListFields() {
        $read = $this->definition->getListFields();
        $expected = [
            'author',
            'title',
            'library'
        ];
        $this->assertSame($read, $expected);
        $read = $this->definitionLibrary->getListFields();
        $expected = [
            'id',
            'created_at',
            'updated_at',
            'name',
            'type',
            'opening',
            'isOpenOnSundays',
            'planet',
            'libraryBook'
        ];
        $this->assertSame($read, $expected);
        $old = $read;
        $expected = [
            'id',
            'name'
        ];
        $this->definitionLibrary->setListFields($expected);
        $read = $this->definitionLibrary->getListFields();
        $this->assertSame($read, $expected);
        $this->definitionLibrary->setListFields($old);
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

    public function testGetSetLocale() {
        $this->definition->setLocale('de');
        $read = $this->definition->getLocale();
        $expected = 'de';
        $this->assertSame($expected, $read);
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
        $expected = [
            ['foo', 'bar', 'bla']
        ];
        $this->assertSame($read, $expected);
    }

    public function testGetSetChildrenLabelFields() {
        $read = $this->definitionLibrary->getChildrenLabelFields();
        $expected = ['book' => 'title'];
        $this->assertSame($read, $expected);
        $expected = ['book' => 'author'];
        $this->definitionLibrary->setChildrenLabelFields($expected);
        $read = $this->definitionLibrary->getChildrenLabelFields();
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
        $expected = [
            'author',
            'title',
            'library'
        ];
        $this->assertSame($read, $expected);
        $expected = [
            'author',
            'title'
        ];
        $this->definition->setFilter($expected);
        $read = $this->definition->getFilter();
        $this->assertSame($read, $expected);
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

    public function testInvalidFieldNames() {
        try {
            $this->definition->setFilter(['foo', 'bar']);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $read = $e->getMessage();
            $expected = 'Invalid fields (foo, bar) in filter, valid ones are: id, created_at, updated_at, title, author, pages, release, library, secondLibrary, cover, price';
            $this->assertSame($expected, $read);
        }
    }

    public function testGetSubTypeField() {
        $read = $this->definitionLibrary->getSubTypeField('libraryBook', 'many', 'entity');
        $expected = 'book';
        $this->assertSame($expected, $read);

        $read = $this->definitionLibrary->getSubTypeField('libraryBook', 'many', 'hideId');
        $this->assertSame(true, $read);

        $read = $this->definition->getSubTypeField('library', 'reference', 'hideId');
        $this->assertSame(true, $read);

        $read = $this->definitionLibrary->getSubTypeField('secondLibrary', 'reference', 'hideId');
        $this->assertSame(null, $read);

        $read = $this->definitionLibrary->getSubTypeField('name', 'many', 'entity');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSubTypeField('libraryBook', 'many', 'foo');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSubTypeField('libraryBook', 'foo', 'entity');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSubTypeField('', 'many', 'entity');
        $this->assertNull($read);

        $read = $this->definitionLibrary->getSubTypeField(null, 'many', 'entity');
        $this->assertNull($read);
    }

    public function testGetEditableFieldNames() {
        $read = $this->definitionLibrary->getEditableFieldNames();
        $expected = ['name', 'type', 'opening', 'isOpenOnSundays', 'planet', 'libraryBook'];
        $this->assertSame($expected, $read);
    }

    public function testGetSetField() {
        $read = $this->definition->getField('author', 'description');
        $expected = 'The Author of the Book';
        $this->assertSame($expected, $read);
        $read = $this->definition->getField('title', 'description');
        $this->assertNull($read);
        $read = $this->definition->getField('foo', 'description');
        $this->assertNull($read);
        $read = $this->definition->getField(null, 'description');
        $this->assertNull($read);
        $read = $this->definition->getField('author', null);
        $this->assertNull($read);
        $read = $this->definition->getField('foo', 'bar');
        $this->assertNull($read);
        $read = $this->definition->getField(null, null);
        $this->assertNull($read);
        $read = $this->definition->getField(null, null, 'foo');
        $expected = 'foo';
        $this->assertSame($expected, $read);

        $expected = 'The Great Author of the Book';
        $this->definition->setField('description', 'author', $expected);
        $read = $this->definition->getField('description', 'author');
        $this->assertSame($expected, $read);
    }

    public function testGetSetNavBarGroup() {
        $read = $this->definition->getNavBarGroup();
        $expected = 'entities';
        $this->assertSame($read, $expected);
        $this->definition->setNavBarGroup('main');
        $read = $this->definition->getNavBarGroup();
        $expected = 'main';
        $this->assertSame($read, $expected);
    }

    public function testHasSetOptimisticLocking() {
        $read = $this->definition->hasOptimisticLocking();
        $this->assertTrue($read);
        $this->definition->setOptimisticLocking(false);
        $read = $this->definition->hasOptimisticLocking();
        $this->assertFalse($read);
    }

}
