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

use CRUDlexTestEnv\CRUDTestDBSetup;
use CRUDlex\CRUDEntity;
use CRUDlex\CRUDEntityValidator;

class CRUDEntityValidatorTest extends \PHPUnit_Framework_TestCase {

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $crudServiceProvider = CRUDTestDBSetup::createCRUDServiceProvider();
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
            'optimisticLocking' => false,
            'fields' => array(
                'title' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'author' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'pages' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'release' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'library' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'secondLibrary' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'cover' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'price' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                )
            )
        );
        $invalid = $valid;
        $invalid['valid'] = false;

        $validatorBook = new CRUDEntityValidator($entityBook);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $valid;
        $this->assertSame($read, $expected);

        $entityBook->set('title', null);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['fields']['title']['required'] = true;
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

        $validLibrary = array(
            'valid' => true,
            'optimisticLocking' => false,
            'fields' => array(
                'name' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'type' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'opening' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'isOpenOnSundays' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                ),
                'planet' => array(
                    'required' => false,
                    'unique' => false,
                    'input' => false
                )
            )
        );
        $invalidLibrary = $validLibrary;
        $invalidLibrary = $validLibrary;
        $invalidLibrary['valid'] = false;

        $entityLibrary2 = $this->dataLibrary->createEmpty();
        $entityLibrary2->set('name', 'lib a');
        $validatorLibrary2 = new CRUDEntityValidator($entityLibrary2);
        $read = $validatorLibrary2->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['fields']['name']['unique'] = true;
        $this->assertSame($read, $expected);

        $entityLibrary1->set('type', 'large');

        $validatorLibrary1 = new CRUDEntityValidator($entityLibrary1);
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('type', 'foo');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['fields']['type']['input'] = true;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('type', null);

        $entityLibrary1->set('opening', '2014-08-31 12:00');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', '2014-08-31 12:00:00');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', 'foo');
        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['fields']['opening']['input'] = true;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', null);

        $read = $validatorLibrary1->validate($this->dataLibrary, 0);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);

        $entityLibrary2->set('name', 'lib b');
        $this->dataLibrary->create($entityLibrary2);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary2->set('name', 'lib a');
        $read = $validatorLibrary2->validate($this->dataLibrary, 0);
        $expected = $invalidLibrary;
        $expected['fields']['name']['unique'] = true;
        $this->assertSame($read, $expected);

        $entityBook->set('pages', 'abc');
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['fields']['pages']['input'] = true;
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
        $expected['fields']['pages']['required'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('pages', 111);

        $entityBook->set('price', 'abc');
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['fields']['price']['input'] = true;
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
        $expected['fields']['release']['input'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('release', '2014-08-31');

        $entityBook->set('library', 666);
        $read = $validatorBook->validate($this->dataBook, 0);
        $expected = $invalid;
        $expected['fields']['library']['input'] = true;
        $this->assertSame($read, $expected, 0);
        $entityBook->set('library', $entityLibrary1->get('id'));
    }

}
