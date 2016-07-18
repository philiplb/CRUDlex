<?php
namespace CRUDlexTestEnv;

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

use CRUDlex\MySQLDataFactory;
use CRUDlex\ServiceProvider;
use CRUDlexTestEnv\NullFileProcessor;

class TestDBSetup {

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
            ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $app['db']->executeUpdate($sql);

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
            '  `release` datetime DEFAULT NULL,';

        if ($useUUIDs) {
            $sql .= '  `library` varchar(36) NOT NULL,'.
                    '  `secondLibrary` varchar(36) DEFAULT NULL,';
        } else {
            $sql .= '  `library` int(11) NOT NULL,'.
                    '  `secondLibrary` int(11) DEFAULT NULL,';
        }

        $sql .= '  `cover` varchar(255) DEFAULT NULL,'.
            '  `price` float DEFAULT NULL,'.
            '  PRIMARY KEY (`id`),'.
            '  CONSTRAINT `book_ibfk_1` FOREIGN KEY (`library`) REFERENCES `library` (`id`),'.
            '  CONSTRAINT `book_ibfk_2` FOREIGN KEY (`secondLibrary`) REFERENCES `library` (`id`)'.
            ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        $app['db']->executeUpdate($sql);

        return $app;
    }

    public static function createServiceProvider($useUUIDs = false) {
        static::$fileProcessor = new NullFileProcessor();
        $app = static::createAppAndDB($useUUIDs);
        $crudServiceProvider = new ServiceProvider();
        $dataFactory = new MySQLDataFactory($app['db'], $useUUIDs);
        $crudFile = __DIR__.'/../crud.yml';
        $crudServiceProvider->init($dataFactory, $crudFile, static::$fileProcessor, true, $app);
        return $crudServiceProvider;
    }

    public static function getFileProcessor() {
        return static::$fileProcessor;
    }

}
