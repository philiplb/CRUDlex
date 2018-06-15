<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlexTestEnv;

use CRUDlex\EntityDefinitionFactory;
use CRUDlex\EntityDefinitionValidator;
use CRUDlex\Service;
use League\Flysystem\Adapter\NullAdapter;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

use Eloquent\Phony\Phpunit\Phony;

use Doctrine\DBAL\Connection;
use CRUDlex\MySQLDataFactory;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;

class TestDBSetup
{

    private static $filesystemHandle;

    public static function getDBConfig()
    {
        return [
            'host'      => '127.0.0.1',
            'dbname'    => 'crudTest',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'driver' => 'pdo_mysql',
        ];
    }

    public static function createDB(Connection $db, $useUUIDs)
    {

        $db->executeUpdate('DROP TABLE IF EXISTS libraryBook;');
        $db->executeUpdate('DROP TABLE IF EXISTS book;');
        $db->executeUpdate('DROP TABLE IF EXISTS library;');

        $db->executeUpdate('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
        $db->executeUpdate('SET time_zone = "+00:00"');

        $sql = 'CREATE TABLE IF NOT EXISTS `library` (';
        if ($useUUIDs) {
            $sql .='  `id` varchar(36) NOT NULL,';
        } else {
            $sql .='  `id` int(11) NOT NULL AUTO_INCREMENT,';
        }
        $sql .= '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
            '  `deleted_at` datetime DEFAULT NULL,'.
            '  `name` varchar(255) DEFAULT NULL,'.
            '  `type` varchar(255) DEFAULT NULL,'.
            '  `opening` datetime DEFAULT NULL,'.
            '  `isOpenOnSundays` tinyint(1) NOT NULL,'.
            '  `planet` varchar(255) DEFAULT NULL,'.
            '  PRIMARY KEY (`id`)'.
            ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
        $db->executeUpdate($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `book` (';

        if ($useUUIDs) {
            $sql .='  `id` varchar(36) NOT NULL,';
        } else {
            $sql .='  `id` int(11) NOT NULL AUTO_INCREMENT,';
        }

        $sql .= '  `created_at` datetime NOT NULL,'.
            '  `updated_at` datetime NOT NULL,'.
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

        $db->executeUpdate($sql);


        $sql = 'CREATE TABLE `libraryBook` (';

        if ($useUUIDs) {
            $sql .= ' `library` varchar(36) NOT NULL,'.
                ' `book` varchar(36) NOT NULL,';
        } else {
            $sql .= ' `library` int(11) NOT NULL,'.
                ' `book` int(11) NOT NULL,';
        }
        $sql .='  KEY `library` (`library`),'.
            '  KEY `book` (`book`),'.
            '  CONSTRAINT `librarybook_ibfk_1` FOREIGN KEY (`library`) REFERENCES `library` (`id`),'.
            '  CONSTRAINT `librarybook_ibfk_2` FOREIGN KEY (`book`) REFERENCES `book` (`id`)'.
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        $db->executeUpdate($sql);
    }

    public static function createAppAndDB($useUUIDs = false)
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), [
            'dbs.options' => [
                'default' => static::getDBConfig()
            ]
        ]);

        static::createDB($app['db'], $useUUIDs);

        $app->register(new LocaleServiceProvider());
        $app->register(new TranslationServiceProvider(), [
            'locale_fallbacks' => ['en'],
        ]);


        return $app;
    }

    public static function createService($useUUIDs = false)
    {
        static::$filesystemHandle = Phony::partialMock('\\League\\Flysystem\\Filesystem', [new NullAdapter()]);
        static::$filesystemHandle->readStream->returns(null);
        static::$filesystemHandle->getMimetype->returns('test');
        static::$filesystemHandle->getSize->returns(42);

        $app = static::createAppAndDB($useUUIDs);
        $crudFile = __DIR__.'/../crud.yml';
        $dataFactory = new MySQLDataFactory($app['db'], $useUUIDs);
        $entityDefinitionFactory = new EntityDefinitionFactory();
        $filesystem = static::$filesystemHandle->get();
        $validator = new EntityDefinitionValidator();

        $service = new Service($crudFile, null, $app['url_generator'], $app['translator'], $dataFactory, $entityDefinitionFactory,  $filesystem, $validator);
        return $service;
    }

    public static function getFilesystemHandle()
    {
        return static::$filesystemHandle;
    }

}
