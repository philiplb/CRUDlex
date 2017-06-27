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

use CRUDlex\MimeTypes;
use CRUDlex\ServiceProvider;
use CRUDlex\EntityDefinitionFactory;

class MimeTypesTest extends \PHPUnit_Framework_TestCase {

    public function testGetMimeType() {
        $mimeTypes = new MimeTypes();

        $read     = $mimeTypes->getMimeType('test.css');
        $expected = 'text/css';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType('test.jpg');
        $expected = 'image/jpeg';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType(__DIR__.'/../../src/static/css/vendor/bootstrap/bootstrap.css');
        $expected = 'text/css';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType(__DIR__.'/../../src/static/images/blank.gif');
        $expected = 'image/gif';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType(__DIR__.'/../../src/static/js/vendor/select2/select2.js');
        $expected = 'application/x-javascript';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType(null);
        $expected = 'application/octet-stream';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType('foo');
        $expected = 'application/octet-stream';
        $this->assertSame($expected, $read);

        $read     = $mimeTypes->getMimeType('');
        $expected = 'application/octet-stream';
        $this->assertSame($expected, $read);

    }

}