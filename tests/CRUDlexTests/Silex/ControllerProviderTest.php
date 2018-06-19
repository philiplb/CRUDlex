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

        $app->boot();
        $app->mount('/crud', new \CRUDlex\Silex\ControllerProvider());

        $this->dataBook = $app['crud']->getData('book');
        $this->dataLibrary = $app['crud']->getData('library');
        return $app;
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
