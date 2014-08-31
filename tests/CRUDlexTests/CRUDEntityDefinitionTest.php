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

use CRUDlex\CRUDServiceProvider;
use CRUDlex\CRUDEntity;
use CRUDlexTestEnv\CRUDTestDataFactory;

class CRUDEntityDefinitionTest extends \PHPUnit_Framework_TestCase {

    protected $definition;

    protected function setUp() {
        $crudServiceProvider = new CRUDServiceProvider();
        $dataFactory = new CRUDTestDataFactory();
        $crudFile = __DIR__.'/../crud.yml';
        $stringsFile = __DIR__.'/../../src/strings.yml';
        $crudServiceProvider->init($dataFactory, $crudFile, $stringsFile);
        $this->definition = $crudServiceProvider->getData('book')->getDefinition();
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

}
