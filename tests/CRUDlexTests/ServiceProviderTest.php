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
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\NullAdapter;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    protected $crudFile;

    protected $dataFactory;

    protected $filesystem;

    protected function setUp()
    {
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
        $this->filesystem = new Filesystem(new NullAdapter());
    }

    protected function createServiceProvider()
    {
        $app = new Application();
        $app['crud.filesystem'] = $this->filesystem;
        $app['crud.datafactory'] = $this->dataFactory;
        $app['crud.file'] = $this->crudFile;
        $crudServiceProvider = new ServiceProvider();
        $crudServiceProvider->boot($app);
        $crudServiceProvider->init(null, $app);
        return $crudServiceProvider;
    }

    public function testRegisterAndBoot()
    {
        $app = new Application();
        $app->register(new ServiceProvider(), [
            'crud.file' => $this->crudFile,
            'crud.datafactory' => $this->dataFactory
        ]);
        $this->assertTrue($app->offsetExists('crud'));
        $app->boot();
        $app['crud']->getEntities();
    }

    public function testInvalidInit()
    {
        $app = new Application();
        $app['crud.file'] = 'foo';
        $crudServiceProvider = new ServiceProvider();
        $crudServiceProvider->boot($app);

        try {
            $crudServiceProvider->init(null, $app);
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            // Wanted.
        }

    }

    public function testGetEntities()
    {
        $crudServiceProvider = $this->createServiceProvider();
        $expected = ['library', 'book'];
        $read = $crudServiceProvider->getEntities();
        $this->assertSame($read, $expected);
    }

    public function testGetEntitiesNavBar()
    {
        $crudServiceProvider = $this->createServiceProvider();
        $expected = ['entities' => ['library', 'book']];
        $read = $crudServiceProvider->getEntitiesNavBar();
        $this->assertSame($read, $expected);
    }

    public function testGetData()
    {
        $crudServiceProvider = $this->createServiceProvider();
        $read = $crudServiceProvider->getData('book');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('library');
        $this->assertNotNull($read);
        $read = $crudServiceProvider->getData('foo');
        $this->assertNull($read);
    }

    public function testSetGetTemplate()
    {

        $crudServiceProvider = new ServiceProvider();
        $crudServiceProvider->setTemplate('template.list.book', 'testTemplateListBook.twig');
        $crudServiceProvider->setTemplate('template.list', 'testTemplateList.twig');
        $crudServiceProvider->setTemplate('layout.list.book', 'testLayoutListBook.twig');
        $crudServiceProvider->setTemplate('layout.list', 'testLayoutList.twig');

        $read = $crudServiceProvider->getTemplate('template', 'list', 'book');
        $this->assertSame($read, 'testTemplateListBook.twig');
        $read = $crudServiceProvider->getTemplate('template', 'list', 'library');
        $this->assertSame($read, 'testTemplateList.twig');
        $read = $crudServiceProvider->getTemplate('layout', 'list', 'book');
        $this->assertSame($read, 'testLayoutListBook.twig');
        $read = $crudServiceProvider->getTemplate('layout', 'list', 'library');
        $this->assertSame($read, 'testLayoutList.twig');

        $expected = '@crud/list.twig';
        $read = $crudServiceProvider->getTemplate('foo', 'list', 'bar');
        $this->assertSame($read, $expected);
        $read = $crudServiceProvider->getTemplate(null, 'list', 'bar');
        $this->assertSame($read, $expected);

        $expected = 'testLayoutList.twig';
        $read = $crudServiceProvider->getTemplate('layout', 'list', null);
        $this->assertSame($read, $expected);

        $expected = '@crud/.twig';
        $read = $crudServiceProvider->getTemplate('layout', null, 'book');
        $this->assertSame($read, $expected);
    }

    public function testGetLocales()
    {
        $crudServiceProvider = new ServiceProvider();
        $expected = ['de', 'el', 'en', 'fr'];
        $read = $crudServiceProvider->getLocales();
        $this->assertSame($read, $expected);
    }

    public function testInitialSort()
    {
        $crudServiceProvider = $this->createServiceProvider();
        $data = $crudServiceProvider->getData('library');
        $read = $data->getDefinition()->isInitialSortAscending();
        $this->assertFalse($read);
        $data = $crudServiceProvider->getData('book');
        $read = $data->getDefinition()->isInitialSortAscending();
        $this->assertTrue($read);
    }

    public function testCustomEntityDefinitionFactory()
    {
        $serviceProvider = new ServiceProvider();
        $app = new Application();

        $entityDefinitionFactoryHandle = Phony::mock('\\CRUDlex\\EntityDefinitionFactory');
        $entityDefinitionFactoryHandle->createEntityDefinition->returns(new \CRUDlex\EntityDefinition(
            '', [
                'isOpenOnSundays' => [],
                'author' => [],
                'title' => [],
                'library' => [],
                'libraryBook' => []
            ], '', '', [], $serviceProvider
        ));
        $entityDefinitionFactoryMock = $entityDefinitionFactoryHandle->get();
        $app['crud.entitydefinitionfactory'] = $entityDefinitionFactoryMock;
        $app['crud.file'] = $this->crudFile;
        $app['crud.datafactory'] = $this->dataFactory;
        $app['crud.filesystem'] = $this->filesystem;
        $serviceProvider->boot($app);
        $serviceProvider->init(null, $app);
        $entityDefinitionFactoryHandle->createEntityDefinition->twice()->called();
    }

    public function testEntityDefinitionValidation()
    {
        $serviceProvider = new ServiceProvider();
        $app = new Application();
        $entityDefinitionValidatorHandle = Phony::mock('\\CRUDlex\\EntityDefinitionValidator');
        $entityDefinitionValidatorMock = $entityDefinitionValidatorHandle->get();
        $app['crud.entitydefinitionvalidator'] = $entityDefinitionValidatorMock;
        $app['crud.file'] = $this->crudFile;
        $app['crud.datafactory'] = $this->dataFactory;
        $app['crud.filesystem'] = $this->filesystem;
        $serviceProvider->boot($app);
        $serviceProvider->init(null, $app);
        $entityDefinitionValidatorHandle->validate->once()->called();

        $app = new Application();
        $app['crud.validateentitydefinition'] = true;
        $app['crud.file'] = $this->crudFile;
        $app['crud.datafactory'] = $this->dataFactory;
        $app['crud.filesystem'] = $this->filesystem;
        $serviceProvider->boot($app);
        $serviceProvider->init(null, $app);
        $entityDefinitionValidatorHandle->validate->once()->called();
    }

    public function testSwitchedOffEntityDefinitionValidation()
    {
        $serviceProvider = new ServiceProvider();
        $app = new Application();
        $entityDefinitionValidatorHandle = Phony::mock('\\CRUDlex\\EntityDefinitionValidator');
        $entityDefinitionValidatorMock = $entityDefinitionValidatorHandle->get();
        $app['crud.validateentitydefinition'] = false;
        $app['crud.entitydefinitionvalidator'] = $entityDefinitionValidatorMock;
        $app['crud.file'] = $this->crudFile;
        $app['crud.datafactory'] = $this->dataFactory;
        $app['crud.filesystem'] = $this->filesystem;
        $serviceProvider->boot($app);
        $serviceProvider->init(null, $app);
        $entityDefinitionValidatorHandle->validate->never()->called();
    }

    public function testSetLocale()
    {
        $serviceProvider = $this->createServiceProvider();
        $serviceProvider->setLocale('de');
        $read = $serviceProvider->getData('library')->getDefinition()->getLocale();
        $expected = 'de';
        $this->assertSame($expected, $read);
        $read = $serviceProvider->getData('book')->getDefinition()->getLocale();
        $this->assertSame($expected, $read);
    }

}
