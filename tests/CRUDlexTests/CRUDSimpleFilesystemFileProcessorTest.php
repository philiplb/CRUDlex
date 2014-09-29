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

use CRUDlexTestEnv\CRUDTestDBSetup;
use CRUDlex\CRUDSimpleFilesystemFileProcessor;

class CRUDSimpleFilesystemFileProcessorTest extends \PHPUnit_Framework_TestCase {

    private $fileProcessor;

    private $dataBook;

    private $dataLibrary;

    private $file1;

    private $file2;

    protected function cleanUpFiles() {
        $dir = null;
        if (file_exists($this->file1)) {
            unlink($this->file1);
            $dir = dirname($this->file1);
        }
        if (file_exists($this->file2)) {
            unlink($this->file2);
            $dir = dirname($this->file2);
        }
        if ($dir) {
            rmdir($dir);
        }
    }

    protected function setUp() {
        $this->fileProcessor = new CRUDSimpleFilesystemFileProcessor();
        $crudServiceProvider = CRUDTestDBSetup::createCRUDServiceProvider();
        $this->dataBook = $crudServiceProvider->getData('book');
        $this->dataLibrary = $crudServiceProvider->getData('library');

        $this->file1 = __DIR__.'/../uploads/book/1/cover/test1A.xml';
        $this->file2 = __DIR__.'/../uploads/book/1/cover/test2A.xml';

        $this->cleanUpFiles();
    }

    public function testCreateFile() {

        $this->cleanUpFiles();

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $file = __DIR__.'/../test1A.xml';
        copy(__DIR__.'/../test1.xml', $file);

        $request = new Request(array(), array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ), array(), array(), array(
            'cover' => new UploadedFile($file, 'test1A.xml', 'application/xml', filesize($file), null, true)
        ));

        $this->fileProcessor->createFile($request, $entityBook, 'book', 'cover');
        $this->assertTrue(file_exists($this->file1));

        $request = new Request(array(), array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id'),
            'cover' => null
        ));

        $this->fileProcessor->createFile($request, $entityBook, 'book', 'cover');
    }

    public function testUpdateFile() {

        $this->cleanUpFiles();

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $file = __DIR__.'/../test1A.xml';
        copy(__DIR__.'/../test1.xml', $file);

        $request = new Request(array(), array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ), array(), array(), array(
            'cover' => new UploadedFile($file, 'test1A.xml', 'application/xml', filesize($file), null, true)
        ));

        $this->fileProcessor->createFile($request, $entityBook, 'book', 'cover');

        $file2 = __DIR__.'/../test2A.xml';
        copy(__DIR__.'/../test2.xml', $file2);

        $request = new Request(array(), array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ), array(), array(), array(
            'cover' => new UploadedFile($file2, 'test2A.xml', 'application/xml', filesize($file2), null, true)
        ));

        $this->fileProcessor->updateFile($request, $entityBook, 'book', 'cover');

        $this->assertTrue(file_exists($this->file1));
        $this->assertTrue(file_exists($this->file2));
    }

    public function testDeleteFile() {

        $this->cleanUpFiles();

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $this->dataBook->create($entityBook);

        $file = __DIR__.'/../test1A.xml';
        copy(__DIR__.'/../test1.xml', $file);

        $request = new Request(array(), array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ), array(), array(), array(
            'cover' => new UploadedFile($file, 'test1A.xml', 'application/xml', filesize($file), null, true)
        ));

        $this->fileProcessor->createFile($request, $entityBook, 'book', 'cover');
        $this->fileProcessor->deleteFile($entityBook, 'book', 'cover');
        // This file processor is defensive, so the file should still exist.
        $this->assertTrue(file_exists($this->file1));
    }

    public function testRenderFile() {

        $this->cleanUpFiles();

        $entityLibrary = $this->dataLibrary->createEmpty();
        $entityLibrary->set('name', 'lib');
        $this->dataLibrary->create($entityLibrary);

        $entityBook = $this->dataBook->createEmpty();
        $entityBook->set('title', 'title');
        $entityBook->set('author', 'author');
        $entityBook->set('pages', 111);
        $entityBook->set('library', $entityLibrary->get('id'));
        $entityBook->set('cover', 'test1A.xml');
        $this->dataBook->create($entityBook);

        $file = __DIR__.'/../test1A.xml';
        copy(__DIR__.'/../test1.xml', $file);

        $request = new Request(array(), array(
            'title' => 'title',
            'author' => 'author',
            'pages' => 111,
            'library' => $entityLibrary->get('id')
        ), array(), array(), array(
            'cover' => new UploadedFile($file, 'test1A.xml', 'application/xml', filesize($file), null, true)
        ));

        $this->fileProcessor->createFile($request, $entityBook, 'book', 'cover');

        ob_start();
        $response = $this->fileProcessor->renderFile($entityBook, 'book', 'cover');
        $read = ob_get_clean();
        $read = str_replace("\r", '', $read); // For testing under Windows.
        $expected = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $expected .= "<test>test1</test>\n";
        $this->assertSame($read, $expected);
    }


}
