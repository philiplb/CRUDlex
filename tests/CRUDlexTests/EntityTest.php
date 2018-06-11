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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class EntityTest extends \PHPUnit_Framework_TestCase
{

    protected $crudService;

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp()
    {
        $this->crudService = TestDBSetup::createService();
        $this->dataBook = $this->crudService->getData('book');
        $this->dataLibrary = $this->crudService->getData('library');
    }

    public function testGetSet()
    {
        $definitionLibrary = $this->crudService->getData('library')->getDefinition();
        $library = $this->crudService->getData('library')->createEmpty();
        $library->set('name', 'lib a');
        $this->crudService->getData('library')->create($library);

        $definition = $this->crudService->getData('book')->getDefinition();
        $entity = new Entity($definition);
        $entity->set('test', 'testdata');
        $read = $entity->get('test');
        $expected = 'testdata';
        $this->assertSame($expected, $read);

        $entity->set('test', 'testdata2');
        $read = $entity->get('test');
        $expected = 'testdata2';
        $this->assertSame($expected, $read);

        $read = $entity->get('testNull');
        $this->assertNull($read);

        $entity->set('price', 3.99);
        $read = $entity->get('price');
        $expected = 3.99;
        $this->assertSame($expected, $read);

        $entity->set('price', '');
        $read = $entity->get('price');
        $expected = null;
        $this->assertSame($expected, $read);

        $entity->set('pages', 111);
        $read = $entity->get('pages');
        $expected = 111;
        $this->assertSame($expected, $read);

        $entity->set('library', $library->get('id'));
        $read = $entity->get('library');
        $expected = $library->get('id');
        $this->assertSame($expected, $read);

        $entity = $this->crudService->getData('book')->createEmpty();
        $entity->set('title', 'title a');
        $entity->set('author', 'author a');
        $entity->set('pages', 1);
        $entity->set('library', $library->get('id'));
        $entity->set('cover', 'cover a');
        $this->crudService->getData('book')->create($entity);

        $library->set('libraryBook', [['id' => $entity->get('id')]]);
        $read = $library->get('libraryBook');
        $expected = [['id' => $entity->get('id')]];
        $this->assertSame($expected, $read);

        // Fixed values override
        $definition->setField('pages', 'value', 666);
        $entity->set('pages', 111);
        $read = $entity->get('pages');
        $expected = 666;
        $this->assertSame($expected, $read);

        $entity = new Entity($definitionLibrary);

        $entity->set('isOpenOnSundays', true);
        $read = $entity->get('isOpenOnSundays');
        $expected = true;
        $this->assertSame($expected, $read);

        $entity->set('opening', '');
        $read = $entity->get('opening');
        $expected = null;
        $this->assertSame($expected, $read);

        $entity->set('name', '');
        $read = $entity->get('name');
        $expected = null;
        $this->assertSame($expected, $read);

        $entity->set('opening', '2016-09-12 01:02:03');
        $read = $entity->get('opening');
        $expected = '2016-09-12 01:02:03';
        $this->assertSame($expected, $read);

    }

    public function testGetRaw()
    {
        $definition = $this->crudService->getData('book')->getDefinition();
        $entity = new Entity($definition);
        $entity->set('test', 'testdata');
        $read = $entity->getRaw('test');
        $expected = 'testdata';
        $this->assertSame($expected, $read);
        $read = $entity->getRaw('test2');
        $this->assertNull($read);
    }

    public function testGetDefinition()
    {
        $entityLibrary = $this->dataLibrary->createEmpty();
        $read = $entityLibrary->getDefinition();
        $expected = $this->dataLibrary->getDefinition();
        $this->assertSame($expected, $read);
    }

    public function testPopulateViaRequest()
    {
        $book = $this->dataBook->createEmpty();
        $file = __DIR__.'/../test1.xml';
        $request = Request::create('', 'POST', [
            'title' => 'a title'
        ], [], [
            'cover' => new UploadedFile($file, 'test1.xml', 'application/xml', filesize($file), null, true)
        ]);
        $book->populateViaRequest($request);

        $read = $book->get('title');
        $expected = 'a title';
        $this->assertSame($expected, $read);

        $read = $book->get('cover');
        $expected = 'test1.xml';
        $this->assertSame($expected, $read);

        $library = $this->dataLibrary->createEmpty();
        $request = Request::create('', 'POST', [
            'libraryBook' => ['3']
        ], [], []);
        $library->populateViaRequest($request);

        $read = $library->get('libraryBook');
        $expected = [['id' => '3']];
        $this->assertSame($expected, $read);
    }

}
