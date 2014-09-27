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

use CRUDlex\CRUDSimpleFilesystemFileProcessor;

class CRUDSimpleFilesystemFileProcessorTest extends \PHPUnit_Framework_TestCase {

    private $fileProcessor;

    protected function setUp() {
        $fileProcessor = new CRUDSimpleFilesystemFileProcessor();
    }

    public function testCreateFile() {
        
    }


}
