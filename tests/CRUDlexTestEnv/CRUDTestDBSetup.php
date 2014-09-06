<?php
namespace CRUDlexTestEnv;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

use CRUDlex\CRUDMySQLDataFactory;
use CRUDlex\CRUDServiceProvider;

class CRUDTestDBSetup {

    public static function createAppAndDB() {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            'dbs.options' => array(
                'default' => array(
                    'host'      => '127.0.0.1',
                    'dbname'    => 'crudTest',
                    'user'      => 'root',
                    'password'  => '',
                    'charset'   => 'utf8',
                )
            ),
        ));

        $app['db']->executeUpdate('DROP TABLE IF EXISTS book;');
        $app['db']->executeUpdate('DROP TABLE IF EXISTS library;');

        $app['db']->executeUpdate('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
        $app['db']->executeUpdate('SET time_zone = "+00:00"');

        $sql = 'CREATE TABLE IF NOT EXISTS `book` ('.
            '  `id` int(11) NOT NULL AUTO_INCREMENT,'.
            '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `version` int(11) NOT NULL,'.
            '  `title` varchar(255) NOT NULL,'.
            '  `author` varchar(255) NOT NULL,'.
            '  `pages` int(11) NOT NULL,'.
            '  `release` datetime DEFAULT NULL,'.
            '  `library` int(11) NOT NULL,'.
            '  PRIMARY KEY (`id`),'.
            '  KEY `library` (`library`)'.
            ') ENGINE=MEMORY  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        $app['db']->executeUpdate($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `library` ('.
            '  `id` int(11) NOT NULL AUTO_INCREMENT,'.
            '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `version` int(11) NOT NULL,'.
            '  `name` varchar(255) NOT NULL,'.
            '  `type` varchar(255) DEFAULT NULL,'.
            '  `opening` datetime DEFAULT NULL,'.
            '  `isOpenOnSundays` tinyint(1) NOT NULL,'.
            '  PRIMARY KEY (`id`)'.
            ') ENGINE=MEMORY  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $app['db']->executeUpdate($sql);
        return $app;
    }

    public static function createCRUDServiceProvider() {
        $app = self::createAppAndDB();
        $crudServiceProvider = new CRUDServiceProvider();
        $dataFactory = new CRUDMySQLDataFactory($app['db']);
        $crudFile = __DIR__.'/../crud.yml';
        $stringsFile = __DIR__.'/../../src/strings.yml';
        $crudServiceProvider->init($dataFactory, $crudFile, $stringsFile);
        return $crudServiceProvider;
    }

}
