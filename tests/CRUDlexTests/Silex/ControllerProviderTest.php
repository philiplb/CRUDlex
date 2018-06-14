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

use CRUDlex\Silex\ServiceProvider;
use League\Flysystem\Adapter\Local;
use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Eloquent\Phony\Phpunit\Phony;

use CRUDlexTestEnv\TestDBSetup;
use CRUDlex\Entity;

class ControllerProviderTest extends WebTestCase
{

    protected $dataBook;

    protected $dataLibrary;

    protected $filesystemHandle;

    public function createApplication()
    {

        $app = TestDBSetup::createAppAndDB();

        $app->register(new \Silex\Provider\SessionServiceProvider());
        $app['session.test'] = true;
        $app['debug'] = true;

        $this->filesystemHandle = Phony::partialMock('\\League\\Flysystem\\Filesystem', [new Local(__DIR__.'/../../tmp')]);
        $filesystemMock = $this->filesystemHandle->get();

        $dataFactory = new \CRUDlex\MySQLDataFactory($app['db']);
        $app->register(new ServiceProvider(), [
            'crud.file' => __DIR__ . '/../../crud.yml',
            'crud.datafactory' => $dataFactory,
            'crud.filesystem' => $filesystemMock
        ]);

        $app->register(new \Silex\Provider\TwigServiceProvider(), [
            'twig.path' => __DIR__.'/../../views'
        ]);

        $app->mount('/crud', new \CRUDlex\Silex\ControllerProvider());
        $app->boot();

        $this->dataBook = $app['crud']->getData('book');
        $this->dataLibrary = $app['crud']->getData('library');
        return $app;
    }

    public function testCreate()
    {
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

        $file = __DIR__.'/../../test1.xml';

        $client->request('POST', '/crud/book/create', [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id'),
            'secondLibrary' => '' // This might occure if the user leaves the form field empty
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Book created with id ")'));

        $books = $this->dataBook->listEntries();
        $this->assertCount(1, $books);

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->never()->called();

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->getEvents()->push('before', 'create', $before);
        $client->request('POST', '/crud/book/create', [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/Could not create\./', $client->getResponse()->getContent());
        $this->dataBook->getEvents()->pop('before', 'create');

        $this->dataBook->getEvents()->push('before', 'createFiles', $before);
        $client->request('POST', '/crud/book/create', [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/Could not create\./', $client->getResponse()->getContent());
        $this->dataBook->getEvents()->pop('before', 'createFiles');

        // Prefilled form
        $client->request('GET', '/crud/book/create?author=myAuthor');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/value="myAuthor"/', $client->getResponse()->getContent());

    }

    public function testShowList()
    {

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
            $entityBookA->set('library', $i % 2 == 0 ? $library->get('id') : $library2->get('id'));
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

        $crawler = $client->request('GET', '/crud/library?crudFilterlibraryBook[]='.$entityBook1Id);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Total: 1")'));
        $this->assertCount(1, $crawler->filter('html:contains("lib a")'));
        $this->assertCount(0, $crawler->filter('html:contains("lib b1")'));
        $this->assertCount(0, $crawler->filter('html:contains("lib b2")'));

        $crawler = $client->request('GET', '/crud/book?crudFilterlibrary='.$library2->get('id'));
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Total: 4")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB1")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB3")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB5")'));
        $this->assertCount(1, $crawler->filter('html:contains("titleB7")'));
    }

    public function testShow()
    {

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

    public function testEdit()
    {
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

        $file = __DIR__.'/../../test1.xml';

        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', [
            'version' => 0,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/'.$entityBook->get('id')));
        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('html:contains("Book edited with id '.$entityBook->get('id').'")'));

        $bookEdited = $this->dataBook->get($entityBook->get('id'));
        $this->assertSame($bookEdited->get('title'), 'titleEdited');

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->never()->called();

        // Optimistic locking
        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', [
            'version' => 0,
            'title' => 'titleEdited2',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/There was a more up to date version of the data available\./', $client->getResponse()->getContent());

        // Optimistic locking switched off
        $this->dataBook->getDefinition()->setOptimisticLocking(false);
        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', [
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test2.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/'.$entityBook->get('id')));
        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('html:contains("Book edited with id '.$entityBook->get('id').'")'));

        $bookEdited = $this->dataBook->get($entityBook->get('id'));
        $this->assertSame($bookEdited->get('title'), 'titleEdited');
        $this->dataBook->getDefinition()->setOptimisticLocking(true);

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };
        $this->dataBook->getEvents()->push('before', 'update', $before);
        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', [
            'version' => 1,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/Could not edit\./', $client->getResponse()->getContent());
        $this->dataBook->getEvents()->pop('before', 'update');

        $this->dataBook->getEvents()->push('before', 'updateFiles', $before);
        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', [
            'version' => 1,
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertRegExp('/Could not edit\./', $client->getResponse()->getContent());
        $this->dataBook->getEvents()->pop('before', 'updateFiles');
    }

    public function testDelete()
    {
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

        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete');
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

        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete', [
            'redirectEntity' => 'library',
            'redirectId' => $library->get('id')
        ]);
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
        $this->dataBook->getEvents()->push('before', 'delete', $before);
        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'titleB');
        $entityBook->set('author', 'authorB');
        $entityBook->set('pages', 111);
        $entityBook->set('release', "2014-08-31");
        $entityBook->set('library', $library->get('id'));
        $this->dataBook->create($entityBook);
        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/'.$entityBook->get('id')));
        $client->followRedirect();
        $this->assertRegExp('/Could not delete\./', $client->getResponse()->getContent());
        $this->dataBook->getEvents()->pop('before', 'delete');

        $this->dataBook->getEvents()->push('before', 'deleteFiles', $before);
        $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/'.$entityBook->get('id')));
        $client->followRedirect();
        $this->assertRegExp('/Could not delete\./', $client->getResponse()->getContent());
        $this->dataBook->getEvents()->pop('before', 'deleteFiles');
    }

    public function testLayouts()
    {
        $client = $this->createClient();

        $this->app['crud']->setTemplate('layout', 'layout.twig');
        $this->app['crud']->setTemplate('layout.book', 'layoutBook.twig');
        $this->app['crud']->setTemplate('layout.create', 'layoutCreate.twig');
        $this->app['crud']->setTemplate('layout.show.library', 'layoutLibraryShow.twig');

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

    public function testRenderFile()
    {
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

        $file = __DIR__.'/../../test1.xml';

        $client->request('POST', '/crud/book/create', [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);

        $crawler = $client->request('GET', '/crud/book/1/title/file');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Instance not found")'));

        $client->request('GET', '/crud/book/1/cover/file');
        $this->assertTrue($client->getResponse()->isOk());

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->once()->called();

    }

    public function testDeleteFile()
    {
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

        $file = __DIR__.'/../../test1.xml';

        $client->request('POST', '/crud/book/create', [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'price' => 3.99,
            'library' => $library->get('id')
        ], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);

        $client->request('POST', '/crud/book/1/cover/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("File could not be deleted.")'));

        $this->dataBook->getDefinition()->setField('cover', 'required', false);

        // Canceling events
        $before = function(Entity $entity) {
            return false;
        };

        $this->dataBook->getEvents()->push('before', 'deleteFile', $before);
        $client->request('POST', '/crud/book/1/cover/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("File could not be deleted.")'));
        $this->dataBook->getEvents()->pop('before', 'deleteFile');

        // Sucessful deletion

        $client->request('POST', '/crud/book/1/cover/delete');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book/1'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("File deleted.")'));

        $this->filesystemHandle->writeStream->once()->called();
        $this->filesystemHandle->readStream->never()->called();


    }

    public function testStatic()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/resource/static');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Resource not found")'));

        $crawler = $client->request('GET', '/crud/resource/static?file=abc');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Resource not found")'));

        $crawler = $client->request('GET', '/crud/resource/static?file=css/../css/vendor/bootstrap/bootstrap.min.css');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Resource not found")'));

        ob_start();
        $client->request('GET', '/crud/resource/static?file=css/vendor/bootstrap/bootstrap.min.css');
        $this->assertTrue($client->getResponse()->isOk());
        $response = ob_get_clean();
        $this->assertTrue(strpos($response, '* Bootstrap v') !== false);

        ob_start();
        $client->request('GET', '/crud/resource/static?file=js/vendor/bootstrap/bootstrap.min.js');
        $this->assertTrue($client->getResponse()->isOk());
        $response = ob_get_clean();
        $this->assertTrue(strpos($response, '* Bootstrap v') !== false);
    }

    public function testSettingsLocale()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/crud/setting/locale/foo?redirect=/crud/book');
        $this->assertTrue($client->getResponse()->isNotFound());
        $this->assertCount(1, $crawler->filter('html:contains("Locale foo not found.")'));

        $client->request('GET', '/crud/setting/locale/de?redirect=/crud/book');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Gesamt: ")'));
        $this->assertCount(1, $crawler->filter('html:contains("Bücher")'));

        $client->request('GET', '/crud/setting/locale/en?redirect=/crud/book');
        $this->assertTrue($client->getResponse()->isRedirect('/crud/book'));
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Total: ")'));
    }

}
