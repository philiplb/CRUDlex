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

use CRUDlex\FileHandler;
use CRUDlexTestEnv\TestDBSetup;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileHandlerTest extends \PHPUnit_Framework_TestCase
{

    protected $dataBook;

    protected $dataLibrary;

    protected function setUp()
    {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $this->dataBook = $crudServiceProvider->getData('book');
        $this->dataLibrary = $crudServiceProvider->getData('library');
    }

    public function testRenderFile()
    {

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $filesystemHandle = TestDBSetup::getFilesystemHandle();

        $fileHandler = new FileHandler($filesystemHandle->get(), $this->dataBook->getDefinition());
        $fileHandler->renderFile($entityBook, 'book', 'cover');

        $filesystemHandle->writeStream->never()->called();
        $filesystemHandle->readStream->once()->called();

    }

    public function testDeleteFiles()
    {

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);
        $filesystemHandle = TestDBSetup::getFilesystemHandle();
        $fileHandler = new FileHandler($filesystemHandle->get(), $this->dataBook->getDefinition());
        $fileHandler->deleteFiles($this->dataBook, $entityBook, 'book');
    }

    public function testDeleteFile()
    {

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $filesystemHandle = TestDBSetup::getFilesystemHandle();
        $fileHandler = new FileHandler($filesystemHandle->get(), $this->dataBook->getDefinition());
        $fileHandler->deleteFile($this->dataBook, $entityBook, 'book', 'cover');
    }

    public function testCreateFiles()
    {

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $request = new Request([], [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ], [], [], [
            'cover' => new UploadedFile(__DIR__.'/../test1.xml', 'test1.xml', null, null, null, true)
        ]);

        $filesystemHandle = TestDBSetup::getFilesystemHandle();
        $fileHandler = new FileHandler($filesystemHandle->get(), $this->dataBook->getDefinition());
        $fileHandler->createFiles($this->dataBook, $request, $entityBook, 'book');

        $filesystemHandle->writeStream->once()->called();
        $filesystemHandle->readStream->never()->called();
    }

    public function testUpdateFiles()
    {

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $request = new Request([], [
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ], [], [], [
            'cover' => new UploadedFile(__DIR__.'/../test1.xml', 'test1.xml', null, null, null, true)
        ]);

        $filesystemHandle = TestDBSetup::getFilesystemHandle();
        $fileHandler = new FileHandler($filesystemHandle->get(), $this->dataBook->getDefinition());
        $fileHandler->updateFiles($this->dataBook, $request, $entityBook, 'book');

        $filesystemHandle->writeStream->once()->called();
        $filesystemHandle->readStream->never()->called();
    }
}
