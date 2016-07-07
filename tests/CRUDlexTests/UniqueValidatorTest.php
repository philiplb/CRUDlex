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

use CRUDlexTestEnv\TestDBSetup;
use CRUDlex\UniqueValidator;

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase {

    protected $dataLibrary;

    protected $entityLibrary;

    protected function setUp() {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->dataLibrary = $crudServiceProvider->getData('library');
        $this->entityLibrary = $this->dataLibrary->createEmpty();
        $this->entityLibrary->set('name', 'lib a');
        $this->dataLibrary->create($this->entityLibrary);
        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib b');
        $this->dataLibrary->create($entityLibrary);
    }

    public function testValidate() {

        $validator = new UniqueValidator();
        $parameters = array($this->dataLibrary, $this->entityLibrary, 'name');
        $read = $validator->isValid('lib a', $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid('lib b', $parameters);
        $this->assertFalse($read);

        $read = $validator->isValid('lib c', $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid(null, $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid('', $parameters);
        $this->assertTrue($read);

    }

}
