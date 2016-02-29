<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use CRUDlexTestEnv\TestDBSetup;
use CRUDlex\Entity;
use CRUDlexTestEnv\NullFileProcessor;

class ControllerProviderTest extends WebTestCase {

    protected $dataBook;

    protected $dataLibrary;

    protected $fileProcessor;

    public function createApplication() {

        $app = TestDBSetup::createAppAndDB();

        $app->register(new Silex\Provider\SessionServiceProvider());
        $app['session.test'] = true;
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $this->fileProcessor =  new NullFileProcessor();

        $dataFactory = new CRUDlex\MySQLDataFactory($app['db']);
        $app->register(new CRUDlex\ServiceProvider(), array(
            'crud.file' => __DIR__ . '/../crud.yml',
            'crud.datafactory' => $dataFactory,
            'crud.fileprocessor' => $this->fileProcessor
        ));
        $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/../views'
        ));

        $app->mount('/crud', new CRUDlex\ControllerProvider());

        $this->dataBook = $app['crud']->getData('book');
        $this->dataLibrary = $app['crud']->getData('library');
        return $app;
    }

    public function testCreate() {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/foo/create');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('GET', '/crud/book/create');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Submit")'));
        $this->assertCount(1, $crawler->filter('html:contains("Author")'));
        $this->assertCount(1, $crawler->filter('html:contains("Pages")'));

        $crawler = $client->request('POST', '/crud/book/create');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Could not create, see the red marked fields.")'));
        $this->assertRegExp('/has-error/', $client->getResponse()->getContent());

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $file = __DIR__.'/../test1.xml';
        $this->fileProcessor->reset();

        $client->request('POST', '/crud/book/create', array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Book created with id ")'));

        $books = $this->dataBook->listEntries();
        $this->assertCount(1, $books);

        $this->assertTrue($this->fileProcessor->isCreateFileCalled());
        $this->assertFalse($this->fileProcessor->isUpdateFileCalled());
        $this->assertFalse($this->fileProcessor->isDeleteFileCalled());
        $this->assertFalse($this->fileProcessor->isRenderFileCalled());

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->pushEvent('before', 'create', $before);
        $client->request('POST', '/crud/book/create', array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/Could not create\./', $client->getResponse()->getContent());
        $this->dataBook->popEvent('before', 'create');

    }

    public function testShowList() {

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $entityBook1 = $this->dataBook->createEmpty();
        $entityBook1->set('title', 'titleA');
        $entityBook1->set('author', 'author');
        $entityBook1->set('pages', 111);
        $entityBook1->set('price', 3.99);
        $entityBook1->set('library', $library->get('id'));
        $this->dataBook->create($entityBook1);

        $entityBook2 = $this->dataBook->createEmpty();
        $entityBook2->set('title', 'titleB');
        $entityBook2->set('author', 'author');
        $entityBook2->set('pages', 111);
        $entityBook2->set('price', 3.99);
        $entityBook2->set('library', $library->get('id'));
        $this->dataBook->create($entityBook2);

        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/foo');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('GET', '/crud/book');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("lib a")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleA")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB")'));

        for ($i = 0; $i < 8; ++$i) {
            $entityBookA = $this->dataBook->createEmpty();
            $entityBookA->set('title', 'titleB'.$i);
            $entityBookA->set('author', 'author'.$i);
            $entityBookA->set('pages', 111);
            $entityBookA->set('price', 3.99);
            $entityBookA->set('library', $library->get('id'));
            $this->dataBook->create($entityBookA);
            sleep(1);
        }

        $this->dataBook->getDefinition()->setPageSize(5);
        $crawler = $client->request('GET', '/crud/book');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("titleA")'));
        $this->assertRegExp('/\>1\</', $client->getResponse()->getContent());
        $this->assertRegExp('/\>2\</', $client->getResponse()->getContent());
        $this->assertSame(strpos('>3<', $client->getResponse()->getContent()), false);

        $crawler = $client->request('GET', '/crud/book?crudPage=1');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("titleB3")'));
        $crawler = $client->request('GET', '/crud/book?crudPage=10');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("titleB3")'));
        $crawler = $client->request('GET', '/crud/book?crudFiltertitle=titleB');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("titleB")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB0")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB1")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB2")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB3")'));

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib b1');
        $library->set('isOpenOnSundays', true);
        $this->dataLibrary->create($library);
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib b2');
        $library->set('isOpenOnSundays', true);
        $this->dataLibrary->create($library);
        $crawler = $client->request('GET', '/crud/library?crudFilterisOpenOnSundays=true');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("lib b1")'));
        $this->assertCount(1, $crawler->filter('html:contains("lib b2")'));
        $this->assertCount(0, $crawler->filter('html:contains("lib a")'));
    }

    public function testShow() {

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

        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/foo/'.$entityBook->get('id'));
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('GET', '/crud/book/666');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $crawler = $client->request('GET', '/crud/book/'.$entityBook->get('id'));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("lib a")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleA")'));
        $this->assertCount(1, $crawler->filter('html:contains("authorA")'));
        $this->assertCount(1, $crawler->filter('html:contains("111")'));
        $this->assertCount(1, $crawler->filter('html:contains("2014-08-31")'));

        $crawler = $client->request('GET', '/crud/library/'.$library->get('id'));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("titleA")'));
    }

    public function testEdit() {
        $client = $this->createClient();

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

        $crawler = $client->request('GET', '/crud/foo/'.$entityBook->get('id').'/edit');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('GET', '/crud/book/666/edit');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $crawler = $client->request('GET', '/crud/book/'.$entityBook->get('id').'/edit');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/titleA/', $client->getResponse()->getContent());
        $this->assertCount(1, $crawler->filter('html:contains("Submit")'));
        $this->assertCount(1, $crawler->filter('html:contains("Author")'));
        $this->assertCount(1, $crawler->filter('html:contains("Pages")'));

        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Could not edit, see the red marked fields.")'));
        $this->assertRegExp('/has-error/', $client->getResponse()->getContent());

        $file = __DIR__.'/../test1.xml';
        $this->fileProcessor->reset();

        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', array(
            'version' => 0,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/'.$entityBook->get('id')));
        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('html:contains("Book edited with id '.$entityBook->get('id').'")'));

        $bookEdited = $this->dataBook->get($entityBook->get('id'));
        $this->assertSame($bookEdited->get('title'), 'titleEdited');


        $this->assertFalse($this->fileProcessor->isCreateFileCalled());
        $this->assertTrue($this->fileProcessor->isUpdateFileCalled());
        $this->assertFalse($this->fileProcessor->isDeleteFileCalled());
        $this->assertFalse($this->fileProcessor->isRenderFileCalled());

        // Optimistic locking
        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', array(
            'version' => 0,
            'title' => 'titleEdited2',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/There was a more up to date version of the data available\./', $client->getResponse()->getContent());

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->pushEvent('before', 'update', $before);
        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', array(
            'version' => 1,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/Could not edit\./', $client->getResponse()->getContent());
        $this->dataBook->popEvent('before', 'update');
    }

    public function testDelete() {
        $client = $this->createClient();

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

        $crawler = $client->request('POST', '/crud/foo/'.$entityBook->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('POST', '/crud/book/666/delete');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $this->dataLibrary->getDefinition()->setDeleteCascade(false);
        $crawler = $client->request('POST', '/crud/library/'.$library->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/library/'.$library->get('id')));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Could not delete Library as it is still referenced by another entity.")'));

        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Book deleted.")'));

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

        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete', array(
            'redirectEntity' => 'library',
            'redirectId' => $library->get('id')
        ));
        $this->assertTrue($client->getResponse()->isRedirect('/crud/library/'.$library->get('id')));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Book deleted.")'));

        $bookDeleted = $this->dataBook->get($entityBook->get('id'));
        $this->assertNull($bookDeleted);

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->pushEvent('before', 'delete', $before);
        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleB');
        $entityBook->set('author', 'authorB');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);
        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/'.$entityBook->get('id')));
        $crawler = $client->followRedirect();
        $this->assertRegExp('/Could not delete\./', $client->getResponse()->getContent());
        $this->dataBook->popEvent('before', 'delete');
    }

    public function testLayouts() {
        $client = $this->createClient();

        $this->app['crud.layout'] = 'layout.twig';
        $this->app['crud.layout.book'] = 'layoutBook.twig';
        $this->app['crud.layout.create'] = 'layoutCreate.twig';
        $this->app['crud.layout.show.library'] = 'layoutLibraryShow.twig';

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $crawler = $client->request('GET', '/crud/library');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Base layout")'));

        $crawler = $client->request('GET', '/crud/book');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Book layout")'));

        $crawler = $client->request('GET', '/crud/library/create');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Create layout")'));

        $crawler = $client->request('GET', '/crud/library/1');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Library show layout")'));
    }

    public function testRenderFile() {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/foo/1/cover/file');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('GET', '/crud/book/666/cover/file');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $file = __DIR__.'/../test1.xml';

        $crawler = $client->request('POST', '/crud/book/create', array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));

        $this->fileProcessor->reset();

        $crawler = $client->request('GET', '/crud/book/1/title/file');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $this->fileProcessor->reset();

        $crawler = $client->request('GET', '/crud/book/1/cover/file');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("rendered file")'));

        $this->assertFalse($this->fileProcessor->isCreateFileCalled());
        $this->assertFalse($this->fileProcessor->isUpdateFileCalled());
        $this->assertFalse($this->fileProcessor->isDeleteFileCalled());
        $this->assertTrue($this->fileProcessor->isRenderFileCalled());

    }

    public function testDeleteFile() {
        $client = $this->createClient();

        $crawler = $client->request('POST', '/crud/foo/1/cover/delete');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Entity not found")'));

        $crawler = $client->request('POST', '/crud/book/666/cover/delete');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $file = __DIR__.'/../test1.xml';

        $crawler = $client->request('POST', '/crud/book/create', array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ), array(
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ));

        $this->fileProcessor->reset();

        $crawler = $client->request('POST', '/crud/book/1/cover/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("File could not be deleted.")'));

        $this->dataBook->getDefinition()->setRequired('cover', false);

        $this->fileProcessor->reset();

        $crawler = $client->request('POST', '/crud/book/1/cover/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("File deleted.")'));

        $this->assertFalse($this->fileProcessor->isCreateFileCalled());
        $this->assertFalse($this->fileProcessor->isUpdateFileCalled());
        $this->assertTrue($this->fileProcessor->isDeleteFileCalled());
        $this->assertFalse($this->fileProcessor->isRenderFileCalled());

    }

    public function testStatic() {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/resource/static');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Resource not found")'));

        $crawler = $client->request('GET', '/crud/resource/static?file=abc');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Resource not found")'));

        ob_start();
        $crawler = $client->request('GET', '/crud/resource/static?file=css/vendor/bootstrap/bootstrap.min.css');
        $this->assertTrue($client->getResponse()->isOk());
        $response = ob_get_clean();
        $this->assertTrue(strpos($response, '* Bootstrap v') !== false);

        ob_start();
        $crawler = $client->request('GET', '/crud/resource/static?file=js/vendor/bootstrap/bootstrap.min.js');
        $this->assertTrue($client->getResponse()->isOk());
        $response = ob_get_clean();
        $this->assertTrue(strpos($response, '* Bootstrap v') !== false);
    }

    public function testSettingsLocale() {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/setting/locale/foo?redirect=/crud/book');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Locale foo not found.")'));

        $crawler = $client->request('GET', '/crud/setting/locale/de?redirect=/crud/book');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Gesamt: ")'));
        $this->assertCount(1, $crawler->filter('html:contains("Bücher")'));

        $crawler = $client->request('GET', '/crud/setting/locale/en?redirect=/crud/book');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Total: ")'));
    }

}
