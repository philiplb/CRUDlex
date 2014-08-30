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

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

use CRUDlex\CRUDMySQLDataFactory;
use CRUDlex\CRUDServiceProvider;
use CRUDlex\CRUDEntity;

class CRUDMySQLDataTest extends \PHPUnit_Framework_TestCase {

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            'dbs.options' => array(
                'default' => array(
                    'host'      => '127.0.0.1',
                    'dbname'    => 'crudTest',
                    'user'      => 'root',
                    'password'  => '',
                    'charset'   => 'utf8',
                )
            ),
        ));

        $app['db']->executeUpdate('DROP TABLE IF EXISTS book;');
        $app['db']->executeUpdate('DROP TABLE IF EXISTS library;');

        $app['db']->executeUpdate('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
        $app['db']->executeUpdate('SET time_zone = "+00:00"');

        $sql = 'CREATE TABLE IF NOT EXISTS `book` ('.
            '  `id` int(11) NOT NULL AUTO_INCREMENT,'.
            '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `version` int(11) NOT NULL,'.
            '  `title` varchar(255) NOT NULL,'.
            '  `author` varchar(255) NOT NULL,'.
            '  `pages` int(11) NOT NULL,'.
            '  `release` datetime DEFAULT NULL,'.
            '  `library` int(11) NOT NULL,'.
            '  PRIMARY KEY (`id`),'.
            '  KEY `library` (`library`)'.
            ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        $app['db']->executeUpdate($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `library` ('.
            '  `id` int(11) NOT NULL AUTO_INCREMENT,'.
            '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `version` int(11) NOT NULL,'.
            '  `name` varchar(255) NOT NULL,'.
            '  PRIMARY KEY (`id`)'.
            ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $app['db']->executeUpdate($sql);

        $crudServiceProvider = new CRUDServiceProvider();
        $dataFactory = new CRUDMySQLDataFactory($app['db']);
        $crudFile = __DIR__.'/../crud.yml';
        $stringsFile = __DIR__.'/../../src/strings.yml';
        $crudServiceProvider->init($dataFactory, $crudFile, $stringsFile);
        $this->dataBook = $crudServiceProvider->getData('book');
        $this->dataLibrary = $crudServiceProvider->getData('library');
    }

    public function testCreate() {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'name');
        $id = $this->dataLibrary->create($entity);
        $this->assertNotNull($id);
        $this->assertTrue($id > 0);
    }

    public function testList() {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameA');
        $this->dataLibrary->create($entity);
        $entity = new CRUDEntity($this->dataBook->getDefinition());
        $entity->set('name', 'nameB');
        $this->dataLibrary->create($entity);
        $list = $this->dataLibrary->listEntries();
        $read = count($list);
        $expected = 2;
        $this->assertSame($read, $expected);
    }

    public function testGet() {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameC');
        $id = $this->dataLibrary->create($entity);
        $entityRead = $this->dataLibrary->get($id);
        $read = $entityRead->get('name');
        $expected = 'nameC';
        $this->assertSame($read, $expected);

        $entity = $this->dataLibrary->get(666);
        $this->assertNull($entity);
    }

    public function testGetDefinition() {
        $definition = $this->dataLibrary->getDefinition();
        $this->assertNotNull($definition);
    }

    public function testCreateEmpty() {
        $entity = $this->dataLibrary->createEmpty();
        $read = $entity->get('id');
        $this->assertNull($read);
    }

}
