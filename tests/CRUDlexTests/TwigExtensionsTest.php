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

}
