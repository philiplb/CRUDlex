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

use CRUDlex\MySQLDataFactory;
use CRUDlex\Silex\ControllerProvider;
use CRUDlex\Silex\ServiceProvider;
use CRUDlexTestEnv\TestDBSetup;
use Eloquent\Phony\Phpunit\Phony;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ControllerProviderTest extends TestCase
{

    public function testConnect()
    {
        $app = new Application();
        $defaultRoute = new Route();
        $controllerCollectionHandle = Phony::partialMock('\\Silex\\ControllerCollection', [$defaultRoute]);
        $app['controllers_factory'] = $controllerCollectionHandle->get();
        $app['session'] = new Session(new MockArraySessionStorage());
        $app->register(new LocaleServiceProvider());
        $app->register(new TranslationServiceProvider(), [
            'locale_fallbacks' => ['en'],
        ]);
        $app->register(new DoctrineServiceProvider(), [
            'dbs.options' => [
                'default' => TestDBSetup::getDBConfig()
            ],
        ]);
        $crudFile = __DIR__.'/../../crud.yml';
        $dataFactory = new MySQLDataFactory($app['db']);
        $app->register(new ServiceProvider(), [
            'crud.file' => $crudFile,
            'crud.datafactory' => $dataFactory
        ]);
        $app->register(new TwigServiceProvider());

        $controllerProvider = new ControllerProvider();
        $controllerProvider->connect($app);
        $controllerCollectionHandle->get->calledWith('/resource/static', '*');
        $controllerCollectionHandle->match->calledWith('/{entity}/create', '*');
        $controllerCollectionHandle->get->calledWith('/{entity}', '*');
        $controllerCollectionHandle->get->calledWith('/{entity}/{id}', '*');
        $controllerCollectionHandle->match->calledWith('/{entity}/{id}/edit', '*');
        $controllerCollectionHandle->post->calledWith('/{entity}/{id}/delete', '*');
        $controllerCollectionHandle->get->calledWith('/{entity}/{id}/{field}/file', '*');
        $controllerCollectionHandle->post->calledWith('/{entity}/{id}/{field}/delete', '*');
        $controllerCollectionHandle->get->calledWith('/setting/locale/{locale}', '*');

        $app['crud.controller'] = 'foo';
        try {
            $controllerProvider->connect($app);
            $this->fail();
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

    }

}
