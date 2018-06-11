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

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $dataLibrary;

    protected $entityLibrary;

    protected $entityBook;

    protected function setUp()
    {
        $crudService = TestDBSetup::createService();
        $this->dataLibrary = $crudService->getData('library');
        $this->entityLibrary = $this->dataLibrary->createEmpty();
        $this->entityLibrary->set('name', 'lib a');
        $this->dataLibrary->create($this->entityLibrary);
        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib b');
        $this->dataLibrary->create($entityLibrary);

        $dataBook = $crudService->getData('book');
        $this->entityBook = $dataBook->createEmpty();
        $this->entityBook->set('title', 'title');
        $this->entityBook->set('author', 'author');
        $this->entityBook->set('pages', 111);
        $this->entityBook->set('library', $entityLibrary->get('id'));
        $dataBook->create($this->entityBook);

        $entityLibrary->set('libraryBook', [['id' => $this->entityBook->get('id')]]);
        $this->dataLibrary->update($entityLibrary);
    }

    public function testValidate()
    {

        $validator = new UniqueValidator();
        $parameters = [$this->dataLibrary, $this->entityLibrary, 'name'];
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

        $parameters[2] = 'libraryBook';

        $read = $validator->isValid([['id' => $this->entityBook->get('id') + 1]], $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid([['id' => $this->entityBook->get('id')]], $parameters);
        $this->assertFalse($read);

    }

    public function testGetInvalidDetails()
    {
        $validator = new UniqueValidator();
        $read = $validator->getInvalidDetails();
        $expected = 'unique';
        $this->assertSame($expected, $read);
    }

}
