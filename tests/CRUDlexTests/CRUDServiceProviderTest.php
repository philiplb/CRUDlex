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
use CRUDlex\CRUDMySQLDataFactory;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class CRUDServiceProviderTest extends \PHPUnit_Framework_TestCase {

    protected $crudFile;

    protected $stringsFile;

    protected $dataFactory;

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
        $this->crudFile = __DIR__.'/../crud.yml';
        $this->stringsFile = __DIR__.'/../../src/strings.yml';
        $this->dataFactory = new CRUDMySQLDataFactory($app['db']);
    }

    public function testBoot() {
        $crudServiceProvider = new CRUDServiceProvider();
        $crudServiceProvider->boot(new Application());
    }

    public function testRegister() {
        $app = new Application();
        $app->register(new CRUDServiceProvider(), array(
            'crud.file' => $this->crudFile,
            'crud.stringsfile' => $this->stringsFile,
            'crud.datafactory' => $this->dataFactory
        ));
        $this->assertTrue($app->offsetExists('crud'));
        $app['crud']->getEntities();
    }

    public function testInvalidInit() {
        $crudServiceProvider = new CRUDServiceProvider();

        $failed = false;
        try {
            $crudServiceProvider->init($this->dataFactory, 'foo', $this->stringsFile);
            $failed = true;
        } catch (\Exception $e) {
        }
        if ($failed) {
            $this->fail('Expected exception');
        }

        $failed = false;
        try {
            $crudServiceProvider->init($this->dataFactory, $this->crudFile, 'foo');
            $failed = true;
        } catch (\Exception $e) {
        }
        if ($failed) {
            $this->fail('Expected exception');
        }
    }

    public function testGetEntities() {
        $crudServiceProvider = new CRUDServiceProvider();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->stringsFile);
        $expected = array('library', 'book');
        $read = $crudServiceProvider->getEntities();
        $this->assertSame($read, $expected);
    }

    public function testGetData() {
        $crudServiceProvider = new CRUDServiceProvider();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->stringsFile);
        $read = $crudServiceProvider->getData('book');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('library');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('foo');
        $this->assertNull($read);
    }

    public function testFormatDate() {
        $crudServiceProvider = new CRUDServiceProvider();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->stringsFile);

        $read = $crudServiceProvider->formatDate('2014-08-30 12:00:00');
        $expected = '2014-08-30';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate('2014-08-30');
        $expected = '2014-08-30';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate('');
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate(null);
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate('foo');
        $expected = 'foo';
        $this->assertSame($read, $expected);
    }

    public function testFormatDateTime() {
        $crudServiceProvider = new CRUDServiceProvider();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->stringsFile);

        $read = $crudServiceProvider->formatDateTime('2014-08-30 12:00:00');
        $expected = '2014-08-30 12:00';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime('2014-08-30 12:00');
        $expected = '2014-08-30 12:00';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime('');
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime(null);
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime('foo');
        $expected = 'foo';
        $this->assertSame($read, $expected);
    }

    public function testTranslate() {
        $crudServiceProvider = new CRUDServiceProvider();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->stringsFile);

        $read = $crudServiceProvider->translate('label.created_at');
        $expected = 'Created at';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->translate('delete.success', array('fail'));
        $expected = 'fail deleted.';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->translate('delete.success', array('fail', 'foo'));
        $expected = 'fail deleted.';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->translate('delete.success', array());
        $expected = '{0} deleted.';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->translate('test');
        $expected = 'test';
        $this->assertSame($read, $expected);
    }

    public function testBasename() {
        $crudServiceProvider = new CRUDServiceProvider();

        $read = $crudServiceProvider->basename('http://www.philiplb.de/foo.txt');
        $expected = 'foo.txt';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->basename('foo.txt');
        $expected = 'foo.txt';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->basename('');
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->basename(null);
        $expected = '';
        $this->assertSame($read, $expected);
    }
}
