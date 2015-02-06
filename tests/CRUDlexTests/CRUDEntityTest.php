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

class CRUDEntityTest extends \PHPUnit_Framework_TestCase {

    protected $crudServiceProvider;

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp() {
        $this->crudServiceProvider = CRUDTestDBSetup::createCRUDServiceProvider();
        $this->dataBook = $this->crudServiceProvider->getData('book');
        $this->dataLibrary = $this->crudServiceProvider->getData('library');
    }

    public function testGetSet() {
        $definition = $this->crudServiceProvider->getData('book')->getDefinition();
        $entity = new CRUDEntity($definition);
        $entity->set('test', 'testdata');
        $read = $entity->get('test');
        $expected = 'testdata';
        $this->assertSame($read, $expected);

        $entity->set('test', 'testdata2');
        $read = $entity->get('test');
        $expected = 'testdata2';
        $this->assertSame($read, $expected);

        $read = $entity->get('testNull');
        $this->assertNull($read);

        $entity->set('price', 3.99);
        $read = $entity->get('price');
        $expected = 3.99;
        $this->assertSame($read, $expected);

        $entity->set('pages', 111);
        $read = $entity->get('pages');
        $expected = 111;
        $this->assertSame($read, $expected);

        // Fixed values override
        $definition->setFixedValue('pages', 666);
        $entity->set('pages', 111);
        $read = $entity->get('pages');
        $expected = 666;
        $this->assertSame($read, $expected);


        $definition = $this->crudServiceProvider->getData('book')->getDefinition();
        $entity = new CRUDEntity($definition);

        $entity->set('isOpenOnSundays', true);
        $read = $entity->get('isOpenOnSundays');
        $expected = true;
        $this->assertSame($read, $expected);

    }

    public function testGetDefinition() {
        $entityLibrary = $this->dataLibrary->createEmpty();
        $read = $entityLibrary->getDefinition();
        $expected = $this->dataLibrary->getDefinition();
        $this->assertSame($read, $expected);
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
        $entityBook->set('cover', 'cover');
        $entityBook->set('price', 3.99);

        $valid =  array(
            'valid' => true,
            'errors' => array(
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

        $read = $entityBook->validate($this->dataBook);
        $expected = $valid;
        $this->assertSame($read, $expected);

        $entityBook->set('title', null);
        $read = $entityBook->validate($this->dataBook);
        $expected = $invalid;
        $expected['errors']['title']['required'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('title', 'title');

        // Fixed values should override this.
        $entityBook->set('title', null);
        $this->dataBook->getDefinition()->setFixedValue('title', 'abc');
        $read = $entityBook->validate($this->dataBook);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('title', 'title');
        $this->dataBook->getDefinition()->setFixedValue('title', null);

        $validLibrary = array(
            'valid' => true,
            'errors' => array(
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
        $read = $entityLibrary2->validate($this->dataLibrary);
        $expected = $invalidLibrary;
        $expected['errors']['name']['unique'] = true;
        $this->assertSame($read, $expected);

        $entityLibrary1->set('type', 'large');
        $read = $entityLibrary1->validate($this->dataLibrary);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('type', 'foo');
        $read = $entityLibrary1->validate($this->dataLibrary);
        $expected = $invalidLibrary;
        $expected['errors']['type']['input'] = true;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('type', null);

        $entityLibrary1->set('opening', '2014-08-31 12:00');
        $read = $entityLibrary1->validate($this->dataLibrary);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', '2014-08-31 12:00:00');
        $read = $entityLibrary1->validate($this->dataLibrary);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', 'foo');
        $read = $entityLibrary1->validate($this->dataLibrary);
        $expected = $invalidLibrary;
        $expected['errors']['opening']['input'] = true;
        $this->assertSame($read, $expected);
        $entityLibrary1->set('opening', null);

        $read = $entityLibrary1->validate($this->dataLibrary);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);

        $entityLibrary2->set('name', 'lib b');
        $this->dataLibrary->create($entityLibrary2);
        $expected = $validLibrary;
        $this->assertSame($read, $expected);
        $entityLibrary2->set('name', 'lib a');
        $read = $entityLibrary2->validate($this->dataLibrary);
        $expected = $invalidLibrary;
        $expected['errors']['name']['unique'] = true;
        $this->assertSame($read, $expected);

        $entityBook->set('pages', 'abc');
        $read = $entityBook->validate($this->dataBook);
        $expected = $invalid;
        $expected['errors']['pages']['input'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('pages', 111);

        $entityBook->set('pages', 0);
        $read = $entityBook->validate($this->dataBook);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('pages', 111);

        $entityBook->set('price', 'abc');
        $read = $entityBook->validate($this->dataBook);
        $expected = $invalid;
        $expected['errors']['price']['input'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('price', 3.99);

        $entityBook->set('price', 0);
        $read = $entityBook->validate($this->dataBook);
        $expected = $valid;
        $this->assertSame($read, $expected);
        $entityBook->set('price', 3.99);

        $entityBook->set('release', 'abc');
        $read = $entityBook->validate($this->dataBook);
        $expected = $invalid;
        $expected['errors']['release']['input'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('release', '2014-08-31');

        $entityBook->set('library', 666);
        $read = $entityBook->validate($this->dataBook);
        $expected = $invalid;
        $expected['errors']['library']['input'] = true;
        $this->assertSame($read, $expected);
        $entityBook->set('library', $entityLibrary1->get('id'));
    }

}
