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
use CRUDlex\ReferenceValidator;

class ReferenceValidatorTest extends \PHPUnit_Framework_TestCase {

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->dataLibrary = $crudServiceProvider->getData('library');
        $this->dataBook = $crudServiceProvider->getData('book');
        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib a');
        $this->dataLibrary->create($entityLibrary);
    }

    public function testValidate() {

        $validator = new ReferenceValidator();
        $parameters = array($this->dataBook, 'library');
        $read = $validator->isValid(1, $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid(2, $parameters);
        $this->assertFalse($read);

        $read = $validator->isValid(null, $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid('', $parameters);
        $this->assertTrue($read);

    }

}
