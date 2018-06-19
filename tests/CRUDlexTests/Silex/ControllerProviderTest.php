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
