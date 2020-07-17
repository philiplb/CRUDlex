<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlexTests\Silex;

use CRUDlex\Controller;
use CRUDlex\Entity;
use CRUDlex\EntityDefinitionFactory;
use CRUDlex\EntityDefinitionValidator;
use CRUDlex\Service;
use CRUDlex\MySQLDataFactory;
use CRUDlexTestEnv\TestDBSetup;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\NullAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Eloquent\Phony\Phpunit\Phony;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig\TwigFilter;

class ControllerTest extends TestCase
{

    private $db;

    private $dataBook;

    private $dataLibrary;

    private $session;

    private $filesystemHandle;

    private $translator;

    protected function setUp()
    {
        $config = new \Doctrine\DBAL\Configuration();
        $this->db = \Doctrine\DBAL\DriverManager::getConnection(TestDBSetup::getDBConfig(), $config);
        TestDBSetup::createDB($this->db);
    }

    protected function createController()
    {

        $this->translator = new Translator('en');

        $locales = Service::getLocales();
        $localeDir = __DIR__.'/../../src/locales';
        $this->translator->addLoader('yaml', new YamlFileLoader());

        foreach ($locales as $locale) {
            $this->translator->addResource('yaml', $localeDir.'/'.$locale.'.yml', $locale);
        }

        $loader = new FilesystemLoader();
        $loader->addPath(__DIR__.'/../../src/views/', 'crud');
        $twig = new Environment($loader);
        $foo = function() {
            return 'foo';
        };
        $this->session = new Session(new MockArraySessionStorage());
        $twig->addFunction(new TwigFunction('crudlex_getCurrentUri', $foo));
        $twig->addFunction(new TwigFunction('crudlex_sessionGet', $foo));
        $session = $this->session;
        $twig->addFunction(new TwigFunction('crudlex_sessionFlashBagGet', function($type) use ($session) {
            return $session->getFlashBag()->get($type);
        }));
        $twig->addFilter(new TwigFilter('trans', $foo));
        $twig->addFilter(new TwigFilter('crudlex_languageName', $foo));
        $twig->addFilter(new TwigFilter('crudlex_formatDate', $foo));
        $twig->addFilter(new TwigFilter('crudlex_formatDateTime', $foo));
        $twig->addFilter(new TwigFilter('crudlex_basename', $foo));
        $twig->addFilter(new TwigFilter('crudlex_float', $foo));
        $twig->addFilter(new TwigFilter('crudlex_arrayColumn', $foo));

        $crudFile = __DIR__.'/../crud.yml';
        $urlGeneratorMock = Phony::mock('Symfony\Component\\Routing\\Generator\\UrlGeneratorInterface');
        $urlGeneratorMock->generate->returns('redirecting');
        $dataFactory = new MySQLDataFactory($this->db);

        $this->filesystemHandle = Phony::partialMock('\\League\\Flysystem\\Filesystem', [new Local(__DIR__.'/../tmp')]);
        $filesystemMock = $this->filesystemHandle->get();

        $validator = new EntityDefinitionValidator();
        $entityDefinitionFactory = new EntityDefinitionFactory();
        $service = new Service($crudFile, null, $urlGeneratorMock->get(), $this->translator, $dataFactory, $entityDefinitionFactory, $filesystemMock, $validator);
        $service->setTemplate('layout', '@crud/layout.twig');
        $this->dataBook = $service->getData('book');
        $this->dataLibrary = $service->getData('library');
        return new Controller($service, $filesystemMock, $twig, $this->session, $this->translator);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetLocaleAndCheckEntity()
    {
        $request = new Request(['entity' => 'library']);
        $controller = $this->createController();
        $response = $controller->setLocaleAndCheckEntity($request);
        $this->assertNull($response);
        $request = new Request(['entity' => 'foo']);
        $response = $controller->setLocaleAndCheckEntity($request);
        $this->assertNotNull($response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreate()
    {
        $controller = $this->createController();

        $request = new Request();
        $response = $controller->create($request, 'nonExistent');
        $this->assertTrue($response->isNotFound());

        $request = new Request();
        $response = $controller->create($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegexp('/Submit/', $response->getContent());
        $this->assertRegexp('/Author/', $response->getContent());
        $this->assertRegexp('/Pages/', $response->getContent());

        $request = new Request();
        $request->setMethod('POST');
        $response = $controller->create($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegexp('/Could not create, see the red marked fields./', $response->getContent());
        $this->assertRegexp('/has-error/', $response->getContent());

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $file = __DIR__.'/../test1.xml';

        $request = new Request([], [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id'),
            'secondLibrary' => '' // This might occure if the user leaves the form field empty
        ],[], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $request->setMethod('POST');
        $response = $controller->create($request, 'book');
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('success');
        $this->assertRegExp('/Book created with id /', $flash[0]);

        $books = $this->dataBook->listEntries();
        $this->assertCount(1, $books);

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->never()->called();

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->getEvents()->push('before', 'create', $before);
        $response = $controller->create($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/Could not create\./', $response->getContent());
        $this->dataBook->getEvents()->pop('before', 'create');

        $this->dataBook->getEvents()->push('before', 'createFiles', $before);
        $response = $controller->create($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/Could not create\./', $response->getContent());
        $this->dataBook->getEvents()->pop('before', 'createFiles');

        // Prefilled form
        $request = new Request(['author' => 'myAuthor']);
        $response = $controller->create($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/value="myAuthor"/', $response->getContent());
    }

    public function testShowList()
    {
        $controller = $this->createController();

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $library2 = $this->dataLibrary->createEmpty();
        $library2->set('name', 'lib b');
        $this->dataLibrary->create($library2);

        $entityBook1 = $this->dataBook->createEmpty();
        $entityBook1->set('title', 'titleA');
        $entityBook1->set('author', 'author');
        $entityBook1->set('pages', 111);
        $entityBook1->set('price', 3.99);
        $entityBook1->set('library', $library->get('id'));
        $this->dataBook->create($entityBook1);
        $entityBook1Id = $entityBook1->get('id');

        $entityBook2 = $this->dataBook->createEmpty();
        $entityBook2->set('title', 'titleB');
        $entityBook2->set('author', 'author');
        $entityBook2->set('pages', 111);
        $entityBook2->set('price', 3.99);
        $entityBook2->set('library', $library->get('id'));
        $this->dataBook->create($entityBook2);

        $library->set('libraryBook', [['id' => $entityBook1Id]]);
        $this->dataLibrary->update($library);

        // Non existing entity

        $request = new Request();
        $response = $controller->showList($request, 'nonExisting');
        $this->assertTrue($response->isNotFound());

        // Default list
        $request = new Request();
        $response = $controller->showList($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/lib a/', $response->getContent());
        $this->assertRegExp('/titleA/', $response->getContent());
        $this->assertRegExp('/titleB/', $response->getContent());

        for ($i = 0; $i < 8; ++$i) {
            $entityBookA = $this->dataBook->createEmpty();
            $entityBookA->set('title', 'titleB'.$i);
            $entityBookA->set('author', 'author'.$i);
            $entityBookA->set('pages', 111);
            $entityBookA->set('price', 3.99);
            $entityBookA->set('library', $i % 2 == 0 ? $library->get('id') : $library2->get('id'));
            $this->dataBook->create($entityBookA);
        }

        // Pagination
        $this->dataBook->getDefinition()->setPageSize(5);
        $response = $controller->showList($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/titleA/', $response->getContent());
        $this->assertRegExp('/\>1\</', $response->getContent());
        $this->assertRegExp('/\>2\</', $response->getContent());
        $this->assertSame(strpos('>3<', $response->getContent()), false);

        $request = new Request(['crudPage' => '1']);
        $response = $controller->showList($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/titleB3/', $response->getContent());

        // Filter
        $request = new Request(['crudFiltertitle' => 'titleB']);
        $response = $controller->showList($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/titleB/', $response->getContent());
        $this->assertRegExp('/titleB0/', $response->getContent());
        $this->assertRegExp('/titleB1/', $response->getContent());
        $this->assertRegExp('/titleB2/', $response->getContent());
        $this->assertRegExp('/titleB3/', $response->getContent());
        $this->assertNotRegExp('/titleA/', $response->getContent());

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib b1');
        $library->set('isOpenOnSundays', true);
        $this->dataLibrary->create($library);
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib b2');
        $library->set('isOpenOnSundays', true);
        $this->dataLibrary->create($library);

        $request = new Request(['crudFilterisOpenOnSundays' => 'true']);
        $response = $controller->showList($request, 'library');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/lib b1/', $response->getContent());
        $this->assertRegExp('/lib b2/', $response->getContent());
        $this->assertNotRegExp('/lib a/', $response->getContent());

        $request = new Request(['crudFilterlibraryBook' => [$entityBook1Id]]);
        $response = $controller->showList($request, 'library');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/lib a/', $response->getContent());
        $this->assertNotRegExp('/lib b1/', $response->getContent());
        $this->assertNotRegExp('/lib b2/', $response->getContent());


        $request = new Request(['crudFilterlibrary' => $library2->get('id')]);
        $response = $controller->showList($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/titleB1/', $response->getContent());
        $this->assertRegExp('/titleB3/', $response->getContent());
        $this->assertRegExp('/titleB5/', $response->getContent());
        $this->assertRegExp('/titleB7/', $response->getContent());
        $this->assertNotRegExp('/titleB0/', $response->getContent());
        $this->assertNotRegExp('/titleB2/', $response->getContent());
        $this->assertNotRegExp('/titleB4/', $response->getContent());
        $this->assertNotRegExp('/titleB6/', $response->getContent());
        $this->assertNotRegExp('/titleB"/', $response->getContent());
        $this->assertNotRegExp('/titleA/', $response->getContent());
    }

    public function testShow()
    {
        $controller = $this->createController();

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleA');
        $entityBook->set('author', 'authorA');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);

        $response = $controller->show('nonExistant', $entityBook->get('id'));
        $this->assertTrue($response->isNotFound());

        $response = $controller->show('book', '666');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Instance not found/', $response->getContent());

        $response = $controller->show('book', $entityBook->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/lib a/', $response->getContent());
        $this->assertRegExp('/titleA/', $response->getContent());
        $this->assertRegExp('/authorA/', $response->getContent());
        $this->assertRegExp('/111/', $response->getContent());


        $response = $controller->show('library', $library->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/titleA/', $response->getContent());
    }

    public function testEdit()
    {
        $controller = $this->createController();

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleA');
        $entityBook->set('author', 'authorA');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);

        $request = new Request();
        $response = $controller->edit($request, 'nonExistent', $entityBook->get('id'));
        $this->assertTrue($response->isNotFound());

        $request = new Request();
        $response = $controller->edit($request, 'book', '666');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Instance not found/', $response);

        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/titleA/', $response->getContent());
        $this->assertRegExp('/Submit/', $response->getContent());
        $this->assertRegExp('/Author/', $response->getContent());
        $this->assertRegExp('/Pages/', $response->getContent());

        $request->setMethod('POST');
        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/Could not edit, see the red marked fields./', $response->getContent());
        $this->assertRegExp('/has-error/', $response->getContent());

        $file = __DIR__.'/../test1.xml';

        $request = new Request([], [
            'version' => 0,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ],[], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $request->setMethod('POST');
        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isRedirect('redirecting'));

        $bookEdited = $this->dataBook->get($entityBook->get('id'));
        $this->assertSame($bookEdited->get('title'), 'titleEdited');

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->never()->called();

        // Optimistic locking
        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/There was a more up to date version of the data available\./', $response->getContent());

        // Optimistic locking switched off
        $this->dataBook->getDefinition()->setOptimisticLocking(false);
        $request = new Request([], [
            'title' => 'titleEdited2',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ],[], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $request->setMethod('POST');
        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isRedirect('redirecting'));

        $bookEdited = $this->dataBook->get($entityBook->get('id'));
        $this->assertSame($bookEdited->get('title'), 'titleEdited2');
        $this->dataBook->getDefinition()->setOptimisticLocking(true);

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->getEvents()->push('before', 'update', $before);
        $request = new Request([], [
            'version' => 1,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ],[], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $request->setMethod('POST');
        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/Could not edit\./', $response->getContent());
        $this->dataBook->getEvents()->pop('before', 'update');

        $this->dataBook->getEvents()->push('before', 'updateFiles', $before);
        $response = $controller->edit($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/Could not edit\./', $response->getContent());
        $this->dataBook->getEvents()->pop('before', 'updateFiles');

    }


    public function testDelete()
    {
        $controller = $this->createController();

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleA');
        $entityBook->set('author', 'authorA');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);

        $request = new Request();
        $request->setMethod('POST');
        $response = $controller->delete($request, 'nonExistant', $library->get('id'));
        $this->assertTrue($response->isNotFound());

        $request = new Request();
        $request->setMethod('POST');
        $response = $controller->delete($request, 'book', '666');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Instance not found\./', $response);

        $this->dataLibrary->getDefinition()->setDeleteCascade(false);
        $response = $controller->delete($request, 'library', $library->get('id'));
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('danger');
        $this->assertRegExp('/Could not delete Library as it is still referenced by another entity./', $flash[0]);

        $response = $controller->delete($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('success');
        $this->assertRegExp('/Book deleted./', $flash[0]);

        $bookDeleted = $this->dataBook->get($entityBook->get('id'));
        $this->assertNull($bookDeleted);

        // Test customizable redirection
        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleA');
        $entityBook->set('author', 'authorA');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);

        $request = new Request([], [
            'redirectEntity' => 'library',
            'redirectId' => $library->get('id')
        ]);
        $controller->delete($request, 'book', $entityBook->get('id'));
        $flash = $this->session->getFlashBag()->get('success');
        $this->assertRegExp('/Book deleted./', $flash[0]);

        $bookDeleted = $this->dataBook->get($entityBook->get('id'));
        $this->assertNull($bookDeleted);

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->getEvents()->push('before', 'delete', $before);
        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleB');
        $entityBook->set('author', 'authorB');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);
        $request = new Request();
        $request->setMethod('POST');
        $controller->delete($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('danger');
        $this->assertRegExp('/Could not delete\./', $flash[0]);
        $this->dataBook->getEvents()->pop('before', 'delete');

        $this->dataBook->getEvents()->push('before', 'deleteFiles', $before);
        $controller->delete($request, 'book', $entityBook->get('id'));
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('danger');
        $this->assertRegExp('/Could not delete\./', $flash[0]);
        $this->dataBook->getEvents()->pop('before', 'deleteFiles');
    }

    public function testRenderFile()
    {
        $controller = $this->createController();

        $response = $controller->renderFile('nonExistant', '1', 'cover');
        $this->assertTrue($response->isNotFound());

        $response = $controller->renderFile('book', '666', 'cover');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Instance not found/', $response->getContent());

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $file = __DIR__.'/../test1.xml';
        $request = new Request();
        $request->setMethod('POST');
        $request = new Request([], [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ],[], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $request->setMethod('POST');
        $controller->create($request, 'book');

        $response = $controller->renderFile('book', '1', 'file');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Instance not found/', $response->getContent());

        $response = $controller->renderFile('book', '1', 'cover');
        $this->assertRegExp('/test1/', $response);

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->once()->called();
    }

    public function testDeleteFile()
    {
        $controller = $this->createController();

        $response = $controller->deleteFile('nonExistant', '1', 'cover');
        $this->assertTrue($response->isNotFound());

        $response = $controller->deleteFile('book', '666', 'cover');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Instance not found/', $response->getContent());

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);
        $file = __DIR__.'/../test1.xml';
        $request = new Request();
        $request->setMethod('POST');
        $request = new Request([], [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ],[], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $request->setMethod('POST');
        $controller->create($request, 'book');

        $response = $controller->deleteFile('book', '1', 'cover');
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('danger');
        $this->assertRegExp('/File could not be deleted\./', $flash[0]);

        $this->dataBook->getDefinition()->setField('cover', 'required', false);

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };

        $this->dataBook->getEvents()->push('before', 'deleteFile', $before);
        $response = $controller->deleteFile('book', '1', 'cover');
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('danger');
        $this->assertRegExp('/File could not be deleted\./', $flash[0]);
        $this->dataBook->getEvents()->pop('before', 'deleteFile');

        // Sucessful deletion

        $response = $controller->deleteFile('book', '1', 'cover');
        $this->assertTrue($response->isRedirect('redirecting'));
        $flash = $this->session->getFlashBag()->get('success');
        $this->assertRegExp('/File deleted\./', $flash[1]); // $flash[0] is the created book

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->never()->called();
        $this->dataBook->getEvents()->pop('before', 'deleteFile');
    }

    public function testStatic()
    {
        $controller = $this->createController();

        $request = new Request();
        $response = $controller->staticFile($request);
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Resource not found\./', $response->getContent());

        $request = new Request(['file' => 'abc']);
        $response = $controller->staticFile($request);
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Resource not found\./', $response->getContent());

        $request = new Request(['file' => 'css/../css/vendor/bootstrap/bootstrap.min.css']);
        $response = $controller->staticFile($request);
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Resource not found\./', $response->getContent());

        ob_start();
        $request = new Request(['file' => 'css/vendor/bootstrap/bootstrap.min.css']);
        $response = $controller->staticFile($request);
        $response->send();
        $content = ob_get_clean();
        $this->assertTrue(strpos($content, '* Bootstrap v') !== false);

        ob_start();
        $request = new Request(['file' => 'js/vendor/bootstrap/bootstrap.min.js']);
        $response = $controller->staticFile($request);
        $response->send();
        $content = ob_get_clean();
        $this->assertTrue(strpos($content, '* Bootstrap v') !== false);
    }

    public function testSetLocale()
    {
        $controller = $this->createController();

        $request = new Request([
            'redirect' => '/crud/book'
        ]);
        $response = $controller->setLocale($request, 'foo');
        $this->assertTrue($response->isNotFound());
        $this->assertRegExp('/Locale foo not found\./', $response->getContent());

        $response = $controller->setLocale($request, 'de');
        $this->translator->setLocale('de');
        $controller->setLocaleAndCheckEntity($request, 'book');
        $this->assertTrue($response->isRedirect('/crud/book'));
        $response = $controller->showList($request, 'book');
        $this->assertTrue($response->isOk());
        $this->assertRegExp('/Titel/', $response->getContent());

        $response = $controller->setLocale($request, 'en');
        $this->translator->setLocale('en');
        $controller->setLocaleAndCheckEntity($request, 'book');
        $this->assertTrue($response->isRedirect('/crud/book'));
        $response = $controller->showList($request, 'book');
        $this->assertRegExp('/Title/', $response->getContent());
    }

}
