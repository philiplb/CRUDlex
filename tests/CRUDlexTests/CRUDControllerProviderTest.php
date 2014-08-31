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

    public function createApplication() {

        $app = CRUDTestDBSetup::createAppAndDB();

        $app->register(new Silex\Provider\SessionServiceProvider());

        $dataFactory = new CRUDlex\CRUDMySQLDataFactory($app['db']);
        $app->register(new CRUDlex\CRUDServiceProvider(), array(
            'crud.file' => __DIR__ . '/../crud.yml',
            'crud.datafactory' => $dataFactory
        ));
        $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new Silex\Provider\TwigServiceProvider());

        $app->mount('/crud', new CRUDlex\CRUDControllerProvider());
        return $app;
    }

    public function testCreate() {
        
    }

}
