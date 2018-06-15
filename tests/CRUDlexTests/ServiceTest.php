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

use CRUDlex\EntityDefinitionFactory;
use CRUDlex\EntityDefinitionValidator;
use CRUDlexTestEnv\TestDBSetup;
use Eloquent\Phony\Phpunit\Phony;

use CRUDlex\Service;
use CRUDlex\MySQLDataFactory;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\NullAdapter;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;

class ServiceTest extends TestCase
{

    protected $crudFile;

    protected $dataFactory;

    protected $filesystem;

    protected $validator;

    protected $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register(new DoctrineServiceProvider(), [
            'dbs.options' => [
                'default' => TestDBSetup::getDBConfig()
            ],
        ]);
        $this->crudFile = __DIR__.'/../crud.yml';
        $this->dataFactory = new MySQLDataFactory($this->app['db']);
        $this->filesystem = new Filesystem(new NullAdapter());
        $this->validator = new EntityDefinitionValidator();
    }

    protected function createService()
    {
        $this->app->register(new LocaleServiceProvider());
        $this->app->register(new TranslationServiceProvider(), [
            'locale_fallbacks' => ['en'],
        ]);
        $entityDefinitionFactory = new EntityDefinitionFactory();
        $service = new Service($this->crudFile, null, $this->app['url_generator'], $this->app['translator'], $this->dataFactory, $entityDefinitionFactory, $this->filesystem, $this->validator);
        return $service;
    }

    public function testInvalidCreate()
    {
        $oldCrudFile = $this->crudFile;
        $this->crudFile = 'foo';
        try {
            $this->createService();
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        $this->crudFile = $oldCrudFile;

    }

    public function testGetData()
    {
        $service = $this->createService();
        $read = $service->getData('book');
        $this->assertNotNull($read);
        $read = $service->getData('library');
        $this->assertNotNull($read);
        $read = $service->getData('foo');
        $this->assertNull($read);
    }

    public function testGetEntities()
    {
        $service = $this->createService();
        $expected = ['library', 'book'];
        $read = $service->getEntities();
        $this->assertSame($read, $expected);
    }

    public function testGetEntitiesNavBar()
    {
        $service = $this->createService();
        $expected = ['entities' => ['library', 'book']];
        $read = $service->getEntitiesNavBar();
        $this->assertSame($read, $expected);
    }

    public function testSetGetTemplate()
    {

        $service = $this->createService();
        $service->setTemplate('template.list.book', 'testTemplateListBook.twig');
        $service->setTemplate('template.list', 'testTemplateList.twig');
        $service->setTemplate('layout.list.book', 'testLayoutListBook.twig');
        $service->setTemplate('layout.list', 'testLayoutList.twig');

        $read = $service->getTemplate('template', 'list', 'book');
        $this->assertSame($read, 'testTemplateListBook.twig');
        $read = $service->getTemplate('template', 'list', 'library');
        $this->assertSame($read, 'testTemplateList.twig');
        $read = $service->getTemplate('layout', 'list', 'book');
        $this->assertSame($read, 'testLayoutListBook.twig');
        $read = $service->getTemplate('layout', 'list', 'library');
        $this->assertSame($read, 'testLayoutList.twig');

        $expected = '@crud/list.twig';
        $read = $service->getTemplate('foo', 'list', 'bar');
        $this->assertSame($read, $expected);
        $read = $service->getTemplate(null, 'list', 'bar');
        $this->assertSame($read, $expected);

        $expected = 'testLayoutList.twig';
        $read = $service->getTemplate('layout', 'list', null);
        $this->assertSame($read, $expected);

        $expected = '@crud/.twig';
        $read = $service->getTemplate('layout', null, 'book');
        $this->assertSame($read, $expected);
    }

    public function testGetLocales()
    {
        $service = $this->createService();
        $expected = ['de', 'el', 'en', 'fr'];
        $read = $service->getLocales();
        $this->assertSame($read, $expected);
    }

    public function testSetLocale()
    {
        $service = $this->createService();
        $service->setLocale('de');
        $read = $service->getData('library')->getDefinition()->getLocale();
        $expected = 'de';
        $this->assertSame($expected, $read);
        $read = $service->getData('book')->getDefinition()->getLocale();
        $this->assertSame($expected, $read);
    }

    public function testSetIsManageI18n()
    {
        $service = $this->createService();
        $this->assertTrue($service->isManageI18n());
        $service->setManageI18n(false);
        $this->assertFalse($service->isManageI18n());
    }

    public function testGenerateURL()
    {
        $urlGeneratorHandle = Phony::mock('\\Symfony\\Component\\Routing\\Generator\\UrlGenerator');
        $urlGeneratorHandle->generate->returns('foo');
        $this->app['url_generator'] = $urlGeneratorHandle->get();
        $service = $this->createService();

        $read = $service->generateURL('list', ['entity' => 'library']);
        $this->assertEquals('foo', $read);
        $urlGeneratorHandle->generate->once()->calledWith('list', ['entity' => 'library']);
    }

    public function testInitialSort()
    {
        $service = $this->createService();
        $data = $service->getData('library');
        $read = $data->getDefinition()->isInitialSortAscending();
        $this->assertFalse($read);
        $data = $service->getData('book');
        $read = $data->getDefinition()->isInitialSortAscending();
        $this->assertTrue($read);
    }

    public function testEntityDefinitionValidation()
    {
        $entityDefinitionValidatorHandle = Phony::mock('\\CRUDlex\\EntityDefinitionValidator');
        $entityDefinitionValidatorMock = $entityDefinitionValidatorHandle->get();
        $this->validator = $entityDefinitionValidatorMock;
        $this->createService();
        $entityDefinitionValidatorHandle->validate->once()->called();
    }
}
