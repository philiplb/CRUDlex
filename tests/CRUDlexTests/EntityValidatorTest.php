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
use CRUDlex\Entity;
use CRUDlex\EntityValidator;

class EntityValidatorTest extends \PHPUnit_Framework_TestCase {

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->dataBook = $crudServiceProvider->getData('book');
        $this->dataLibrary = $crudServiceProvider->getData('library');
    }

    public function testValidate() {

        $entityLibrary1 = $this->dataLibrary->createEmpty();
        $entityLibrary1->set('name', 'lib a');
        $this->dataLibrary->create($entityLibrary1);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary1->get('id'));
        $entityBook->set('secondLibrary', $entityLibrary1->get('id'));
        $entityBook->set('cover', 'cover');
        $entityBook->set('price', 3.99);

        $valid =  array(
            'valid' => true,
            'errors' => array()
        );
        $invalid = $valid;
        $invalid['valid'] = false;

        $validatorBook = new EntityValidator($entityBook);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);

        $entityBook->set('title', null);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['errors']['title'] = array('required');
        $this->assertSame($read, $expected);
        $entityBook->set('title', 'title');

        // Fixed values should override this.
        $entityBook->set('title', null);
        $this->dataBook->getDefinition()->setFixedValue('title', 'abc');
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('title', 'title');
        $this->dataBook->getDefinition()->setFixedValue('title', null);

        $invalidLibrary = $valid;
        $invalidLibrary['valid'] = false;

        $entityLibrary2 = $this->dataLibrary->createEmpty();
        $entityLibrary2->set('name', 'lib a');
        $validatorLibrary2 = new EntityValidator($entityLibrary2);
        $read = $validatorLibrary2->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['errors']['name'] = array('unique');
        $this->assertSame($read, $expected);

        $entityLibrary1->set('type', 'large');

        $validatorLibrary1 = new EntityValidator($entityLibrary1);
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('type', 'foo');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['errors']['type'] = array('inSet');
        $this->assertSame($read, $expected);
        $entityLibrary1->set('type', null);

        $entityLibrary1->set('opening', '2014-08-31 12:00');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', '2014-08-31 12:00:00');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', 'foo');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['errors']['opening'] = array(array('or' => array('dateTime', 'dateTime')));
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', null);

        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);

        $entityLibrary2->set('name', 'lib b');
        $this->dataLibrary->create($entityLibrary2);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityLibrary2->set('name', 'lib a');
        $read = $validatorLibrary2->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['errors']['name'] = array('unique');
        $this->assertSame($read, $expected);

        $entityBook->set('pages', 'abc');
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['errors']['pages'] = array('integer');
        $this->assertSame($read, $expected);
        $entityBook->set('pages', 111);

        $entityBook->set('pages', 0);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('pages', 111);

        $entityBook->set('pages', null);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['errors']['pages'] = array('required');
        $this->assertSame($read, $expected);
        $entityBook->set('pages', 111);

        $entityBook->set('price', 'abc');
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['errors']['price'] = array('floating');
        $this->assertSame($read, $expected);
        $entityBook->set('price', 3.99);

        $entityBook->set('price', 0);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('price', 3.99);

        $entityBook->set('price', null);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('price', 3.99);

        $entityBook->set('release', 'abc');
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['errors']['release'] = array('dateTime');
        $this->assertSame($read, $expected);
        $entityBook->set('release', '2014-08-31');

        $entityBook->set('library', 666);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['errors']['library'] = array('reference');
        $this->assertSame($read, $expected, 0);
        $entityBook->set('library', $entityLibrary1->get('id'));
    }

}
