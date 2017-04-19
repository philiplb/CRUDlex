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

use CRUDlex\YamlReader;
use Eloquent\Phony\Phony;

class YamlReaderTest extends \PHPUnit_Framework_TestCase {

    public function testInvalidFile() {
        $reader = new YamlReader(null);
        try {
            $reader->read('foo');
        } catch (\Exception $e) {
            // Wanted.
        }
    }

    public function testReadEmptyFile() {
        $reader = new YamlReader(null);
        $reader->read(__DIR__.'/../emptyCrud.yml');
    }

    public function testRead() {
        if (file_exists(__DIR__.'/../tmp/crud.ymlCRUDlexCache.php')) {
            unlink(__DIR__.'/../tmp/crud.ymlCRUDlexCache.php');
        }
        $readerHandle = Phony::partialMock('\\CRUDlex\\YamlReader', [__DIR__.'/../tmp']);
        $reader = $readerHandle->get();

        $result = $reader->read( __DIR__.'/../crud.yml');
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('library', $result);
        $readerHandle->writeToCache->once()->called();
        $readerHandle->readFromCache->once()->called();
        $readerHandle->readFromCache->firstCall()->returned(null);

        $result = $reader->read( __DIR__.'/../crud.yml');
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('library', $result);
        $readerHandle->readFromCache->twice()->called();
        $readerHandle->readFromCache->callAt(1)->returned($result);
    }

}
