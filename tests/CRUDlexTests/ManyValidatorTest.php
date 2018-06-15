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
use CRUDlex\ManyValidator;
use PHPUnit\Framework\TestCase;

class ManyValidatorTest extends TestCase
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

        $validator = new ManyValidator();
        $parameters = [$this->dataLibrary, 'libraryBook'];
        $read = $validator->isValid([['id' => $this->entityBook->get('id')]], $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid([['id' => $this->entityBook->get('id') + 1]], $parameters);
        $this->assertFalse($read);

        $read = $validator->isValid(null, $parameters);
        $this->assertTrue($read);

        $read = $validator->isValid('', $parameters);
        $this->assertTrue($read);

    }

    public function testGetInvalidDetails()
    {
        $validator = new ManyValidator();
        $read = $validator->getInvalidDetails();
        $expected = 'many';
        $this->assertSame($expected, $read);
    }

}
