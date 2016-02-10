<?php
namespace CRUDlexTestEnv;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

use CRUDlex\CRUDMySQLDataFactory;
use CRUDlex\CRUDServiceProvider;
use CRUDlexTestEnv\CRUDNullFileProcessor;

class CRUDTestDBSetup {

    private static $fileProcessor;

    public static function createAppAndDB($useUUIDs = false) {
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

        $sql = 'CREATE TABLE IF NOT EXISTS `book` (';

        if ($useUUIDs) {
            $sql .='  `id` varchar(36) NOT NULL,';
        } else {
            $sql .='  `id` int(11) NOT NULL AUTO_INCREMENT,';
        }

        $sql .= '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `version` int(11) NOT NULL,'.
            '  `title` varchar(255) NOT NULL,'.
            '  `author` varchar(255) NOT NULL,'.
            '  `pages` int(11) NOT NULL,'.
            '  `release` datetime DEFAULT NULL,'.
            '  `library` int(11) NOT NULL,'.
            '  `secondLibrary` int(11) DEFAULT NULL,'.
            '  `cover` varchar(255) DEFAULT NULL,'.
            '  `price` float DEFAULT NULL,'.
            '  PRIMARY KEY (`id`),'.
            '  KEY `library` (`library`)'.
            ') ENGINE=MEMORY  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        $app['db']->executeUpdate($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `library` (';
        if ($useUUIDs) {
            $sql .='  `id` varchar(36) NOT NULL,';
        } else {
            $sql .='  `id` int(11) NOT NULL AUTO_INCREMENT,';
        }
        $sql .= '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `version` int(11) NOT NULL,'.
            '  `name` varchar(255) NOT NULL,'.
            '  `type` varchar(255) DEFAULT NULL,'.
            '  `opening` datetime DEFAULT NULL,'.
            '  `isOpenOnSundays` tinyint(1) NOT NULL,'.
            '  `planet` varchar(255) DEFAULT NULL,'.
            '  PRIMARY KEY (`id`)'.
            ') ENGINE=MEMORY  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $app['db']->executeUpdate($sql);
        return $app;
    }

    public static function createCRUDServiceProvider($useUUIDs = false) {
        static::$fileProcessor = new CRUDNullFileProcessor();
        $app = static::createAppAndDB($useUUIDs);
        $crudServiceProvider = new CRUDServiceProvider();
        $dataFactory = new CRUDMySQLDataFactory($app['db'], $useUUIDs);
        $crudFile = __DIR__.'/../crud.yml';
        $crudServiceProvider->init($dataFactory, $crudFile, static::$fileProcessor, true, $app);
        return $crudServiceProvider;
    }

    public static function getFileProcessor() {
        return static::$fileProcessor;
    }

}
