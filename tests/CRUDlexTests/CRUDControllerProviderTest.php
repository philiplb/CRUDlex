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

use CRUDlexTestEnv\CRUDTestDBSetup;

class CRUDControllerProviderTest extends WebTestCase {

    protected $dataBook;

    protected $dataLibrary;

    public function createApplication() {

        $app = CRUDTestDBSetup::createAppAndDB();

        $app->register(new Silex\Provider\SessionServiceProvider());
        $app['session.test'] = true;
        $app['debug'] = true;
        $app['exception_handler']->disable();

        $dataFactory = new CRUDlex\CRUDMySQLDataFactory($app['db']);
        $app->register(new CRUDlex\CRUDServiceProvider(), array(
            'crud.file' => __DIR__ . '/../crud.yml',
            'crud.datafactory' => $dataFactory
        ));
        $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/../views'
        ));

        $app->mount('/crud', new CRUDlex\CRUDControllerProvider());

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

        $crawler = $client->request('POST', '/crud/book/create', array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $library->get('id')
        ));
        $this->assertCount(1, $crawler->filter('html:contains("Book created with id ")'));

        $books = $this->dataBook->listEntries();
        $this->assertCount(1, $books);
    }

    public function testShowList() {

        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'lib a');
        $this->dataLibrary->create($library);

        $entityBook1 = $this->dataBook->createEmpty();
        $entityBook1->set('title', 'titleA');
        $entityBook1->set('author', 'author');
        $entityBook1->set('pages', 111);
        $entityBook1->set('library', $library->get('id'));
        $this->dataBook->create($entityBook1);

        $entityBook2 = $this->dataBook->createEmpty();
        $entityBook2->set('title', 'titleB');
        $entityBook2->set('author', 'author');
        $entityBook2->set('pages', 111);
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

        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/edit', array(
            'title' => 'titleEdited',
            'author' => 'author',
            'pages' => 111,
            'library' => $library->get('id')
        ));
        $this->assertCount(1, $crawler->filter('html:contains("Book edited with id '.$entityBook->get('id').'")'));

        $bookEdited = $this->dataBook->get($entityBook->get('id'));
        $this->assertSame($bookEdited->get('title'), 'titleEdited');

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

        $crawler = $client->request('POST', '/crud/library/'.$library->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Could not delete Library as it is still referenced by another entity.")'));

        $crawler = $client->request('POST', '/crud/book/'.$entityBook->get('id').'/delete');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('html:contains("Book deleted.")'));

        $bookDeleted = $this->dataBook->get($entityBook->get('id'));
        $this->assertNull($bookDeleted);

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

}
