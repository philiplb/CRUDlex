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
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

class TwigExtensionsTest extends \PHPUnit_Framework_TestCase {

    protected $app;

    protected function setUp() {
        $this->app = new Application();
        $this->app->register(new TwigServiceProvider());
        $twigExtensions = new TwigExtensions();
        $twigExtensions->registerTwigExtensions($this->app);
    }

    public function testArrayColumn() {
        $filter = $this->app['twig']->getFilter('arrayColumn');

        $read = call_user_func($filter->getCallable(), [['id' => 1], ['id' => 2], ['id' => 3]], 'id');
        $expected = [1, 2, 3];
        $this->assertSame($expected, $read);
    }

    public function testLanguageName() {
        $filter = $this->app['twig']->getFilter('languageName');

        $read = call_user_func($filter->getCallable(), 'en');
        $expected = 'English';
        $this->assertSame($expected, $read);

        $expected = 'Deutsch';
        $read = call_user_func($filter->getCallable(), 'de');
        $this->assertSame($read, $expected);

        $read = call_user_func($filter->getCallable(), 'invalid');
        $this->assertNull($read);
    }


    public function testFormatFloat() {
        $filter = $this->app['twig']->getFilter('float');

        $float = 0.000004;
        $read = call_user_func($filter->getCallable(), $float);
        $expected = '0.000004';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), null);
        $this->assertNull($read);

        $read = call_user_func($filter->getCallable(), 1.0);
        $expected = '1.0';
        $this->assertSame($expected, $read);

        $float = 0.004;
        $read = call_user_func($filter->getCallable(), $float);
        $expected = '0.004';
        $this->assertSame($expected, $read);
    }

    public function testBasename() {
        $filter = $this->app['twig']->getFilter('basename');

        $read = call_user_func($filter->getCallable(), 'http://www.philiplb.de/foo.txt');
        $expected = 'foo.txt';
        $this->assertSame($read, $expected);

        $read = call_user_func($filter->getCallable(), 'foo.txt');
        $expected = 'foo.txt';
        $this->assertSame($read, $expected);

        $read = call_user_func($filter->getCallable(), '');
        $expected = '';
        $this->assertSame($read, $expected);

        $read = call_user_func($filter->getCallable(), null);
        $expected = '';
        $this->assertSame($read, $expected);
    }

    public function testFormatDate() {
        $filter = $this->app['twig']->getFilter('formatDate');

        $read = call_user_func($filter->getCallable(), '2014-08-30 12:00:00', false);
        $expected = '2014-08-30';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), '2014-08-30', false);
        $expected = '2014-08-30';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), '', false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), null, false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), 'foo', false);
        $expected = 'foo';
        $this->assertSame($expected, $read);

        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('America/Adak');
        $read = call_user_func($filter->getCallable(), '2016-02-01 00:00:00', true);
        $expected = '2016-01-31';
        $this->assertSame($expected, $read);
        date_default_timezone_set($previousTimezone);
    }

    public function testFormatDateTime() {
        $filter = $this->app['twig']->getFilter('formatDateTime');

        $read = call_user_func($filter->getCallable(), '2014-08-30 12:00:00', false);
        $expected = '2014-08-30 12:00';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), '2014-08-30 12:00', false);
        $expected = '2014-08-30 12:00';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), '', false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), null, false);
        $expected = '';
        $this->assertSame($expected, $read);

        $read = call_user_func($filter->getCallable(), 'foo', false);
        $expected = 'foo';
        $this->assertSame($expected, $read);

        $previousTimezone = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');
        $read = call_user_func($filter->getCallable(), '2016-02-01 12:00', true);
        $expected = '2016-02-01 13:00';
        $this->assertSame($expected, $read);
        date_default_timezone_set($previousTimezone);
    }

}
