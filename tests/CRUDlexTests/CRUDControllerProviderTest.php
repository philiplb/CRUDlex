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
        $app->register(new Silex\Provider\TwigServiceProvider());

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

}
