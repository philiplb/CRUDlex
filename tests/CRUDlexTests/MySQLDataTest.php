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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use CRUDlexTestEnv\TestDBSetup;
use CRUDlex\Entity;
use CRUDlex\AbstractData;

class MySQLDataTest extends \PHPUnit_Framework_TestCase
{

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp()
    {
        $crudService = TestDBSetup::createService();
        $this->dataBook = $crudService->getData('book');
        $this->dataLibrary = $crudService->getData('library');
    }

    public function testCreate()
    {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'name');
        $this->dataLibrary->create($entity);
        $id = $entity->get('id');
        $this->assertNotNull($id);
        $this->assertTrue($id > 0);
    }

    public function testCreateWithUUID()
    {
        $crudService = TestDBSetup::createService(true);
        $dataLibrary = $crudService->getData('library');

        $entity = $dataLibrary->createEmpty();
        $entity->set('name', 'name');
        $dataLibrary->create($entity);
        $id = $entity->get('id');
        $this->assertNotNull($id);
        $this->assertTrue(strlen($id) == 36);

        $this->setUp();
    }

    public function testList()
    {
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'nameA');
        $this->dataLibrary->create($library);

        $book = $this->dataBook->createEmpty();
        $book->set('title', 'title');
        $book->set('author', 'author');
        $book->set('pages', 111);
        $book->set('library', $library->get('id'));
        $this->dataBook->create($book);

        $library->set('libraryBook', [['id' => $book->get('id')]]);
        $this->dataLibrary->update($library);

        $entity = new Entity($this->dataBook->getDefinition());
        $entity->set('name', 'nameB');
        $this->dataLibrary->create($entity);

        $list = $this->dataLibrary->listEntries(['name' => 'nameA'], ['name' => '=']);
        $read = $list[0]->get('libraryBook');
        $expected = [['id' => $book->get('id'), 'name' => $book->get('title')]];
        $this->assertSame($expected, $read);

        $list = $this->dataLibrary->listEntries(['libraryBook' => [['id' => $book->get('id')]]], ['libraryBook' => '=']);
        $read = count($list);
        $expected = 1;
        $this->assertSame($expected, $read);

        $list = $this->dataLibrary->listEntries();
        $read = count($list);
        $expected = 2;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries(['name' => 'nameB'], ['name' => '=']);
        $read = count($list);
        $expected = 1;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries(['name' => 'nameB', 'id' => 2], ['name' => '=', 'id' => '=']);
        $read = count($list);
        $expected = 1;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries(['type' => null], ['type' => '=']);
        $read = count($list);
        $expected = 2;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries(['name' => '%eB%'], ['name' => 'LIKE']);
        $read = count($list);
        $expected = 1;
        $this->assertSame($expected, $read);

        $list = $this->dataLibrary->listEntries([], [], null, null, 'name');
        $expected = 'nameB';
        $this->assertSame($expected, $list[0]->get('name'));
        $expected = 'nameA';
        $this->assertSame($expected, $list[1]->get('name'));

        // Sorting by many fields should fall back to the initial sort field
        $list = $this->dataLibrary->listEntries([], [], null, null, 'libraryBook');
        $expected = 'nameB';
        $this->assertSame($expected, $list[0]->get('name'));
        $expected = 'nameA';
        $this->assertSame($expected, $list[1]->get('name'));

        for ($i = 0; $i < 15; ++$i) {
            $entity->set('name', 'name'.$i);
            $this->dataLibrary->create($entity);
        }
        $list = $this->dataLibrary->listEntries([], [], null, null);
        $read = count($list);
        $expected = 17;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries([], [], null, 5);
        $read = count($list);
        $expected = 5;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries([], [], 0, 5);
        $read = count($list);
        $expected = 5;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries([], [], 15, 5);
        $read = count($list);
        $expected = 2;
        $this->assertSame($expected, $read);
        $list = $this->dataLibrary->listEntries([], [], 5, null);
        $read = count($list);
        $expected = 12;
        $this->assertSame($expected, $read);

        // Test for references
        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $libraryId = $entityLibrary->get('id');

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $libraryId);
        $entityBook->set('secondLibrary', $libraryId);
        $this->dataBook->create($entityBook);

        $read = $entityBook->get('library');
        $expected = ['id' => $libraryId, 'name' => 'lib'];
        $this->assertSame($read, $expected);

        $read = $entityBook->get('secondLibrary');
        $expected = ['id' => $libraryId];
        $this->assertSame($read, $expected);
    }

    public function testGet()
    {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameC');
        $this->dataLibrary->create($entity);
        $id = $entity->get('id');
        $entityRead = $this->dataLibrary->get($id);
        $read = $entityRead->get('name');
        $expected = 'nameC';
        $this->assertSame($read, $expected);

        $entity = $this->dataLibrary->get(666);
        $this->assertNull($entity);
    }

    public function testGetDefinition()
    {
        $definition = $this->dataLibrary->getDefinition();
        $this->assertNotNull($definition);
    }

    public function testCreateEmpty()
    {
        $entity = $this->dataLibrary->createEmpty();
        $read = $entity->get('id');
        $this->assertNull($read);
    }

    public function testUpdate()
    {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameUpdate');
        $this->dataLibrary->create($entity);

        $entity->set('name', 'nameUpdated!');
        $this->dataLibrary->update($entity);
        $entityWritten = $this->dataLibrary->get($entity->get('id'));
        $read = $entityWritten->get('name');
        $expected = 'nameUpdated!';
        $this->assertSame($read, $expected);
    }

    public function testDelete()
    {
        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameDelete');
        $this->dataLibrary->create($entity);

        $deleted = $this->dataLibrary->delete($entity);
        $read = $this->dataLibrary->get($entity->get('id'));
        $expected = AbstractData::DELETION_SUCCESS;
        $this->assertSame($deleted, $expected);
        $this->assertNull($read);

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'nameParentTestDelete');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);
        $deleted = $this->dataBook->delete($entityBook);
        $read = $this->dataLibrary->get($entityBook->get('id'));
        $expected = AbstractData::DELETION_SUCCESS;
        $this->assertSame($deleted, $expected);
        $this->assertNull($read);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $this->dataLibrary->getDefinition()->setDeleteCascade(false);
        $deleted = $this->dataLibrary->delete($entityLibrary);
        $expected = AbstractData::DELETION_FAILED_STILL_REFERENCED;
        $this->assertSame($deleted, $expected);
        $deleted = $this->dataBook->delete($entityBook);
        $expected = AbstractData::DELETION_SUCCESS;
        $this->assertSame($deleted, $expected);
        $deleted = $this->dataLibrary->delete($entityLibrary);
        $expected = AbstractData::DELETION_SUCCESS;
        $this->assertSame($deleted, $expected);

        $this->dataLibrary->getDefinition()->setDeleteCascade(true);

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'nameParentTestDelete');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $this->dataBook->getEvents()->push('before', 'delete', function(Entity $entity) {
            return false;
        });
        $deleted = $this->dataLibrary->delete($entityLibrary);
        $expected = AbstractData::DELETION_FAILED_EVENT;
        $this->assertSame($deleted, $expected);
        $entityBook2 = $this->dataBook->get($entityBook->get('id'));
        $this->assertNotNull($entityBook2);

        $this->dataBook->getEvents()->pop('before', 'delete');
        $deleted = $this->dataLibrary->delete($entityLibrary);
        $expected = AbstractData::DELETION_SUCCESS;
        $this->assertSame($deleted, $expected);
        $entityBook2 = $this->dataLibrary->get($entityBook->get('id'));
        $this->assertNull($entityBook2);
    }

    public function testGetIdToNameMap()
    {
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'A');
        $this->dataLibrary->create($library);
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'B');
        $this->dataLibrary->create($library);
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'C');
        $this->dataLibrary->create($library);

        $referenceEntity = $this->dataBook->getDefinition()->getSubTypeField('library', 'reference', 'entity');
        $nameField = $this->dataBook->getDefinition()->getSubTypeField('library', 'reference', 'nameField');
        $read = $this->dataBook->getIdToNameMap($referenceEntity, $nameField);
        $expected = [
            '1' => 'A',
            '2' => 'B',
            '3' => 'C',
        ];
        $this->assertSame($read, $expected);

        $read = $this->dataBook->getIdToNameMap($referenceEntity, null);
        $expected = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
        ];
        $this->assertSame($read, $expected);
    }

    public function testCountBy()
    {
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'A');
        $this->dataLibrary->create($library);
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'B');
        $this->dataLibrary->create($library);
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'C');
        $this->dataLibrary->create($library);

        $table = $this->dataLibrary->getDefinition()->getTable();

        $book = $this->dataBook->createEmpty();
        $book->set('title', 'title');
        $book->set('author', 'author');
        $book->set('pages', 111);
        $book->set('library', $library->get('id'));
        $this->dataBook->create($book);

        $library->set('libraryBook', [['id' => $book->get('id')]]);
        $this->dataLibrary->update($library);

        $read = $this->dataLibrary->countBy(
            $table,
            ['libraryBook' => [['id' => $book->get('id')]]],
            ['libraryBook' => '='],
            true
        );
        $expected = 1;
        $this->assertSame($expected, $read);

        $this->dataLibrary->delete($library);

        $read = $this->dataLibrary->countBy(
            $table,
            [],
            [],
            false
        );
        $expected = 3;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
            $table,
            [],
            [],
            true
        );
        $expected = 2;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
                $table,
                ['id' => 1],
                ['id' => '='],
                false
            );
        $expected = 1;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
                $table,
                ['id' => 1],
                ['id' => '!='],
                false
            );
        $expected = 2;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
                $table,
                ['id' => 1, 'name' => 'A'],
                ['id' => '=', 'name' => '='],
                false
            );
        $expected = 1;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
                $table,
                ['id' => 1, 'name' => 'B'],
                ['id' => '=', 'name' => '='],
                false
            );
        $expected = 0;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
                $table,
                ['id' => 3],
                ['id' => '='],
                false
            );
        $expected = 1;
        $this->assertSame($read, $expected);

        $read = $this->dataLibrary->countBy(
                $table,
                ['id' => 3],
                ['id' => '='],
                true
            );
        $expected = 0;
        $this->assertSame($read, $expected);
    }

    public function testBoolHandling()
    {
        $libraryA = $this->dataLibrary->createEmpty();
        $libraryA->set('name', 'lib');
        $this->dataLibrary->create($libraryA);

        $read = $this->dataLibrary->get($libraryA->get('id'))->get('isOpenOnSundays');
        $this->assertFalse($read);

        $libraryB = $this->dataLibrary->createEmpty();
        $libraryB->set('name', 'lib');
        $libraryB->set('isOpenOnSundays', '1');
        $this->dataLibrary->create($libraryB);

        $read = $this->dataLibrary->get($libraryB->get('id'))->get('isOpenOnSundays');
        $this->assertTrue($read);

        $libraryA->set('isOpenOnSundays', '1');
        $this->dataLibrary->update($libraryA);

        $read = $this->dataLibrary->get($libraryA->get('id'))->get('isOpenOnSundays');
        $this->assertTrue($read);

        $libraryB->set('isOpenOnSundays', null);
        $this->dataLibrary->update($libraryB);

        $read = $this->dataLibrary->get($libraryB->get('id'))->get('isOpenOnSundays');
        $this->assertFalse($read);
    }

    public function testCreateEvents()
    {
        $beforeCalled = false;
        $beforeEvent = function(Entity $entity) use (&$beforeCalled) {
            $beforeCalled = true;
            return true;
        };
        $this->dataLibrary->getEvents()->push('before', 'create', $beforeEvent);

        $afterCalled = false;
        $afterEvent = function(Entity $entity) use (&$afterCalled) {
            $afterCalled = true;
            return true;
        };
        $this->dataLibrary->getEvents()->push('after', 'create', $afterEvent);

        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'name');
        $this->dataLibrary->create($entity);

        $this->assertNotNull($entity->get('id'));
        $this->assertTrue($beforeCalled);
        $this->assertTrue($afterCalled);


        $beforeEvent = function(Entity $entity) {
            return false;
        };
        $this->dataLibrary->getEvents()->push('before', 'create', $beforeEvent);

        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'name');
        $this->dataLibrary->create($entity);
        $id = $entity->get('id');
        $this->assertNull($id);

        $this->dataLibrary->getEvents()->pop('before', 'create');
        $this->dataLibrary->getEvents()->pop('before', 'create');
        $this->dataLibrary->getEvents()->pop('after', 'create');
    }

    public function testUpdateEvents()
    {

        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameUpdate');
        $this->dataLibrary->create($entity);

        $beforeCalled = false;
        $beforeEvent = function(Entity $entity) use (&$beforeCalled) {
            $beforeCalled = true;
            return true;
        };
        $this->dataLibrary->getEvents()->push('before', 'update', $beforeEvent);

        $afterCalled = false;
        $afterEvent = function(Entity $entity) use (&$afterCalled) {
            $afterCalled = true;
            return true;
        };
        $this->dataLibrary->getEvents()->push('after', 'update', $afterEvent);
        $entity->set('name', 'newName');
        $this->dataLibrary->update($entity);

        $dbEntity = $this->dataLibrary->get($entity->get('id'));
        $read = $dbEntity->get('name');
        $expected = 'newName';
        $this->assertSame($read, $expected);

        $this->assertTrue($beforeCalled);
        $this->assertTrue($afterCalled);


        $beforeEvent = function(Entity $entity) {
            return false;
        };
        $this->dataLibrary->getEvents()->push('before', 'update', $beforeEvent);

        $entity->set('name', 'newName2');
        $this->dataLibrary->update($entity);
        $dbEntity = $this->dataLibrary->get($entity->get('id'));
        $read = $dbEntity->get('name');
        $expected = 'newName';
        $this->assertSame($read, $expected);

        $this->dataLibrary->getEvents()->pop('before', 'update');
        $this->dataLibrary->getEvents()->pop('before', 'update');
        $this->dataLibrary->getEvents()->pop('after', 'update');
    }

    public function testDeleteEvents()
    {

        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameDelete');
        $this->dataLibrary->create($entity);

        $beforeCalled = false;
        $beforeEvent = function(Entity $entity) use (&$beforeCalled) {
            $beforeCalled = true;
            return true;
        };
        $this->dataLibrary->getEvents()->push('before', 'delete', $beforeEvent);

        $afterCalled = false;
        $afterEvent = function(Entity $entity) use (&$afterCalled) {
            $afterCalled = true;
            return true;
        };
        $this->dataLibrary->getEvents()->push('after', 'delete', $afterEvent);
        $this->dataLibrary->delete($entity);

        $dbEntity = $this->dataLibrary->get($entity->get('id'));
        $this->assertNull($dbEntity);

        $this->assertTrue($beforeCalled);
        $this->assertTrue($afterCalled);

        $beforeEvent = function(Entity $entity) use (&$beforeCalled) {
            return false;
        };
        $this->dataLibrary->getEvents()->push('before', 'delete', $beforeEvent);

        $entity = $this->dataLibrary->createEmpty();
        $entity->set('name', 'nameDelete2');
        $this->dataLibrary->create($entity);
        $this->dataLibrary->delete($entity);
        $dbEntity = $this->dataLibrary->get($entity->get('id'));
        $this->assertNotNull($dbEntity);
        $this->assertSame($entity->get('id'), $dbEntity->get('id'));

        $this->dataLibrary->getEvents()->pop('before', 'delete');
        $this->dataLibrary->getEvents()->pop('before', 'delete');
        $this->dataLibrary->getEvents()->pop('after', 'delete');
    }

    public function testHasManySet()
    {
        $library = $this->dataLibrary->createEmpty();
        $library->set('name', 'nameA');
        $this->dataLibrary->create($library);
        $library2 = $this->dataLibrary->createEmpty();
        $library2->set('name', 'nameB');
        $this->dataLibrary->create($library2);

        $book = $this->dataBook->createEmpty();
        $book->set('title', 'title');
        $book->set('author', 'author');
        $book->set('pages', 111);
        $book->set('library', $library->get('id'));
        $this->dataBook->create($book);

        $book2 = $this->dataBook->createEmpty();
        $book2->set('title', 'title1');
        $book2->set('author', 'author');
        $book2->set('pages', 111);
        $book2->set('library', $library->get('id'));
        $this->dataBook->create($book2);

        $book3 = $this->dataBook->createEmpty();
        $book3->set('title', 'title2');
        $book3->set('author', 'author');
        $book3->set('pages', 111);
        $book3->set('library', $library2->get('id'));
        $this->dataBook->create($book3);

        $library->set('libraryBook', [['id' => $book->get('id')], ['id' => $book2->get('id')]]);
        $this->dataLibrary->update($library);

        $library2->set('libraryBook', [['id' => $book3->get('id')]]);
        $this->dataLibrary->update($library2);

        $read = $this->dataLibrary->hasManySet('libraryBook', [$book->get('id'), $book2->get('id')]);
        $this->assertTrue($read);

        $read = $this->dataLibrary->hasManySet('libraryBook', [$book->get('id'), $book2->get('id')], $library->get('id'));
        $this->assertFalse($read);

        $read = $this->dataLibrary->hasManySet('libraryBook', [$book->get('id'), $book2->get('id')], $library2->get('id'));
        $this->assertTrue($read);
    }

}
