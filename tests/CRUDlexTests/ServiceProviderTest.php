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

use Eloquent\Phony\Phpunit\Phony;

use CRUDlex\ServiceProvider;
use CRUDlex\MySQLDataFactory;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase {

    protected $crudFile;

    protected $dataFactory;

    protected $fileProcessorMock;

    protected function setUp() {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), [
            'dbs.options' => [
                'default' => [
                    'host'      => '127.0.0.1',
                    'dbname'    => 'crudTest',
                    'user'      => 'root',
                    'password'  => '',
                    'charset'   => 'utf8',
                ]
            ],
        ]);
        $this->crudFile = __DIR__.'/../crud.yml';
        $this->dataFactory = new MySQLDataFactory($app['db']);

        $fileProcessorHandle = Phony::mock('\\CRUDlex\\SimpleFilesystemFileProcessor');
        $this->fileProcessorMock = $fileProcessorHandle->get();

    }

    public function testRegister() {
        $app = new Application();
        $app->register(new ServiceProvider(), [
            'crud.file' => $this->crudFile,
            'crud.datafactory' => $this->dataFactory
        ]);
        $this->assertTrue($app->offsetExists('crud'));
        $app['crud']->getEntities();
    }

    public function testInvalidInit() {
        $app = new Application();
        $crudServiceProvider = new ServiceProvider();

        try {
            $crudServiceProvider->init($this->dataFactory, 'foo', $this->fileProcessorMock, true, $app);
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            // Wanted.
        }

    }

    public function testInitWithEmptyFile() {
        $app = new Application();
        $crudServiceProvider = new ServiceProvider();
        $crudServiceProvider->init($this->dataFactory, __DIR__.'/../emptyCrud.yml', $this->fileProcessorMock, true, $app);
    }

    public function testGetEntities() {
        $crudServiceProvider = new ServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);
        $expected = ['library', 'book'];
        $read = $crudServiceProvider->getEntities();
        $this->assertSame($read, $expected);
    }

    public function testGetData() {
        $crudServiceProvider = new ServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);
        $read = $crudServiceProvider->getData('book');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('library');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('foo');
        $this->assertNull($read);
    }

    public function testFormatDate() {
        $crudServiceProvider = new ServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);

        $read = $crudServiceProvider->formatDate('2014-08-30 12:00:00', false);
        $expected = '2014-08-30';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate('2014-08-30', false);
        $expected = '2014-08-30';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate('', false);
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate(null, false);
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDate('foo', false);
        $expected = 'foo';
        $this->assertSame($read, $expected);

        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('America/Adak');
        $read = $crudServiceProvider->formatDate('2016-02-01 00:00:00', true);
        $expected = '2016-01-31';
        $this->assertSame($read, $expected);
        date_default_timezone_set($previousTimezone);
    }

    public function testFormatDateTime() {
        $crudServiceProvider = new ServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);

        $read = $crudServiceProvider->formatDateTime('2014-08-30 12:00:00', false);
        $expected = '2014-08-30 12:00';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime('2014-08-30 12:00', false);
        $expected = '2014-08-30 12:00';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime('', false);
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime(null, false);
        $expected = '';
        $this->assertSame($read, $expected);

        $read = $crudServiceProvider->formatDateTime('foo', false);
        $expected = 'foo';
        $this->assertSame($read, $expected);

        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');
        $read = $crudServiceProvider->formatDateTime('2016-02-01 12:00', true);
        $expected = '2016-02-01 13:00';
        $this->assertSame($read, $expected);
        date_default_timezone_set($previousTimezone);
    }

    public function testBasename() {
        $crudServiceProvider = new ServiceProvider();

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
        $crudServiceProvider = new ServiceProvider();

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

    public function testIsManagingI18n() {
        $crudServiceProvider = new ServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);
        $read = $crudServiceProvider->isManagingI18n();
        $this->assertTrue($read);
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, false, $app);
        $read = $crudServiceProvider->isManagingI18n();
        $this->assertFalse($read);
    }

    public function testFormatFloat() {
        $float = 0.000004;
        $crudServiceProvider = new ServiceProvider();
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

    public function testGetLocales() {
        $crudServiceProvider = new ServiceProvider();
        $expected = ['de', 'el', 'en'];
        $read = $crudServiceProvider->getLocales();
        $this->assertSame($read, $expected);
    }

    public function testGetLanguageName() {
        $crudServiceProvider = new ServiceProvider();
        $expected = 'Deutsch';
        $read = $crudServiceProvider->getLanguageName('de');
        $this->assertSame($read, $expected);
        $read = $crudServiceProvider->getLanguageName('invalid');
        $this->assertNull($read);
    }

    public function testInitialSort() {
        $crudServiceProvider = new ServiceProvider();
        $app = new Application();
        $crudServiceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);
        $data = $crudServiceProvider->getData('library');
        $read = $data->getDefinition()->getInitialSortField();
        $expected = 'name';
        $read = $data->getDefinition()->isInitialSortAscending();
        $this->assertFalse($read);
        $data = $crudServiceProvider->getData('book');
        $read = $data->getDefinition()->getInitialSortField();
        $expected = 'id';
        $read = $data->getDefinition()->isInitialSortAscending();
        $this->assertTrue($read);
    }

    public function testCustomEntityDefinitionFactory() {
        $serviceProvider = new ServiceProvider();
        $app = new Application();

        $entityDefinitionFactoryHandle = Phony::mock('\\CRUDlex\\EntityDefinitionFactory');
        $entityDefinitionFactoryHandle->createEntityDefinition->returns(new \CRUDlex\EntityDefinition(
            '', [], '', '', [], $serviceProvider
        ));
        $entityDefinitionFactoryMock = $entityDefinitionFactoryHandle->get();
        $app['crud.entitydefinitionfactory'] = $entityDefinitionFactoryMock;
        $serviceProvider->init($this->dataFactory, $this->crudFile, $this->fileProcessorMock, true, $app);
        $entityDefinitionFactoryHandle->createEntityDefinition->once();
    }

}
