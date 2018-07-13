<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlexTests;

use CRUDlex\MySQLDataFactory;
use CRUDlexTestEnv\TestDBSetup;
use PHPUnit\Framework\TestCase;

class MySQLDataFactoryTest extends TestCase
{

    public function testCreateData()
    {
        $crudService = TestDBSetup::createService();
        $config = new \Doctrine\DBAL\Configuration();
        $db = \Doctrine\DBAL\DriverManager::getConnection(TestDBSetup::getDBConfig(), $config);
        $factory = new MySQLDataFactory($db);
        $data = $factory->createData($crudService->getData('book')->getDefinition(), TestDBSetup::getFilesystemHandle()->get());
        $this->assertInstanceOf('\\CRUDlex\\MySQLData', $data);
    }

}
