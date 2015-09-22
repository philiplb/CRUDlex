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
use CRUDlexTestEnv\CRUDNullFileProcessor;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class CRUDServiceProviderTest extends \PHPUnit_Framework_TestCase {

    protected $crudFile;

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
            'crud.datafactory' => $this->dataFactory
        ));
        $this->assertTrue($app->offsetExists('crud'));
        $app['crud']->getEntities();
    }

    public function testInvalidInit() {
        $app = new Application();
        $crudServiceProvider = new CRUDServiceProvider();

        try {
            $crudServiceProvider->init($this->dataFactory, 'foo', new CRUDNullFileProcessor(), true, $app);
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            // Wanted.
        }

    }

    public function testGetEntities() {
        $crudServiceProvider = new CRUDServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, new CRUDNullFileProcessor(), true, $app);
        $expected = array('library', 'book');
        $read = $crudServiceProvider->getEntities();
        $this->assertSame($read, $expected);
    }

    public function testGetData() {
        $crudServiceProvider = new CRUDServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, new CRUDNullFileProcessor(), true, $app);
        $read = $crudServiceProvider->getData('book');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('library');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('foo');
        $this->assertNull($read);
    }

    public function testFormatDate() {
        $crudServiceProvider = new CRUDServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, new CRUDNullFileProcessor(), true, $app);

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
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, new CRUDNullFileProcessor(), true, $app);

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

    public function testGetTemplate() {
        $app = new Application();
        $app['crud.template.list.book'] = 'testTemplateListBook.twig';
        $app['crud.template.list'] = 'testTemplateList.twig';
        $app['crud.layout.list.book'] = 'testLayoutListBook.twig';
        $app['crud.layout.list'] = 'testLayoutList.twig';
        $crudServiceProvider = new CRUDServiceProvider();

        $read = $crudServiceProvider->getTemplate($app, 'template', 'list', 'book');
        $this->assertSame($read, $app['crud.template.list.book']);
        $read = $crudServiceProvider->getTemplate($app, 'template', 'list', 'library');
        $this->assertSame($read, $app['crud.template.list']);
        $read = $crudServiceProvider->getTemplate($app, 'layout', 'list', 'book');
        $this->assertSame($read, $app['crud.layout.list.book']);
        $read = $crudServiceProvider->getTemplate($app, 'layout', 'list', 'library');
        $this->assertSame($read, $app['crud.layout.list']);

        $expected = '@crud/list.twig';
        $read = $crudServiceProvider->getTemplate($app, 'foo', 'list', 'bar');
        $this->assertSame($read, $expected);
        $read = $crudServiceProvider->getTemplate($app, null, 'list', 'bar');
        $this->assertSame($read, $expected);

        $expected = 'testLayoutList.twig';
        $read = $crudServiceProvider->getTemplate($app, 'layout', 'list', null);
        $this->assertSame($read, $expected);

        $expected = '@crud/.twig';
        $read = $crudServiceProvider->getTemplate($app, 'layout', null, 'book');
        $this->assertSame($read, $expected);
    }

    public function testGetManageI18n() {
        $crudServiceProvider = new CRUDServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, new CRUDNullFileProcessor(), true, $app);
        $read = $crudServiceProvider->getManageI18n();
        $this->assertTrue($read);
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, new CRUDNullFileProcessor(), false, $app);
        $read = $crudServiceProvider->getManageI18n();
        $this->assertFalse($read);
    }

    public function testFormatFloat() {
        $float = 0.000004;
        $crudServiceProvider = new CRUDServiceProvider();
        $read = $crudServiceProvider->formatFloat($float);
        $expected = '0.000004';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatFloat(null);
        $this->assertNull($read);

        $read = $crudServiceProvider->formatFloat(1.0);
        $expected = '1.0';
        $this->assertSame($read, $expected);

        $float = 0.004;
        $read = $crudServiceProvider->formatFloat($float);
        $expected = '0.004';
        $this->assertSame($read, $expected);
    }

}
