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

use CRUDlex\StreamedFileResponse;
use PHPUnit\Framework\TestCase;

class StreamedFileResponseTest extends TestCase
{

    public function testGetStreamedFileFunction()
    {
        $sfr = new StreamedFileResponse();
        $response = $sfr->getStreamedFileFunction(__DIR__.'/../test1.xml');
        ob_start();
        $response();
        $actual = ob_get_clean();
        $expected = file_get_contents(__DIR__.'/../test1.xml');
        $this->assertEquals($expected, $actual);
    }

}
