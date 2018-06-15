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

use CRUDlex\TwigExtensions;
use PHPUnit\Framework\TestCase;

class TwigExtensionsTest extends TestCase
{

    public function testLanguageName()
    {
        $twigExtensions = new TwigExtensions();

        $read = $twigExtensions->getLanguageName('en');
        $expected = 'English';
        $this->assertSame($expected, $read);

        $expected = 'Deutsch';
        $read = $twigExtensions->getLanguageName('de');
        $this->assertSame($read, $expected);

        $read = $twigExtensions->getLanguageName('invalid');
        $this->assertNull($read);
    }


    public function testFormatFloat()
    {
        $twigExtensions = new TwigExtensions();

        $float = 0.000004;
        $read = $twigExtensions->formatFloat($float);
        $expected = '0.000004';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatFloat(null);
        $this->assertNull($read);

        $read = $twigExtensions->formatFloat(1.0);
        $expected = '1.0';
        $this->assertSame($expected, $read);

        $float = 0.004;
        $read = $twigExtensions->formatFloat($float);
        $expected = '0.004';
        $this->assertSame($expected, $read);
    }

    public function testFormatDate()
    {
        $twigExtensions = new TwigExtensions();

        $read = $twigExtensions->formatDate('2014-08-30 12:00:00', false);
        $expected = '2014-08-30';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDate('2014-08-30', false);
        $expected = '2014-08-30';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDate('', false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDate(null, false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDate('foo', false);
        $expected = 'foo';
        $this->assertSame($expected, $read);

        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('America/Adak');
        $read = $twigExtensions->formatDate('2016-02-01 00:00:00', true);
        $expected = '2016-01-31';
        $this->assertSame($expected, $read);
        date_default_timezone_set($previousTimezone);
    }

    public function testFormatDateTime()
    {
        $twigExtensions = new TwigExtensions();

        $read = $twigExtensions->formatDateTime('2014-08-30 12:00:00', false);
        $expected = '2014-08-30 12:00';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDateTime('2014-08-30 12:00', false);
        $expected = '2014-08-30 12:00';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDateTime('', false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDateTime(null, false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = $twigExtensions->formatDateTime('foo', false);
        $expected = 'foo';
        $this->assertSame($expected, $read);

        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');
        $read = $twigExtensions->formatDateTime('2016-02-01 12:00', true);
        $expected = '2016-02-01 13:00';
        $this->assertSame($expected, $read);
        date_default_timezone_set($previousTimezone);
    }

}
