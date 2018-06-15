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
use CRUDlex\MySQLDataFactory;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\NullAdapter;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

class ServiceProviderTest extends TestCase
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
        $this->crudFile = __DIR__.'/../../crud.yml';
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
        $expected = ['library', 'book'];
        $acutal = $app['crud']->getEntities();
        $this->assertEquals($expected, $acutal);
    }

    public function testInvalidInit()
    {
        $app = new Application();
        $app['crud.file'] = 'foo';
        $crudServiceProvider = new ServiceProvider();
        $crudServiceProvider->boot($app);
        $app->register(new ServiceProvider(), [
            'crud.file' => 'foo',
            'crud.datafactory' => $this->dataFactory
        ]);

        try {
            $app['crud']->getData('bar');
            $this->fail('Expected exception');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

    }

}
