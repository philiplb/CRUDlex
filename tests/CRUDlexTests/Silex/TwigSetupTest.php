<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlexTests\Silex;

use CRUDlex\Silex\TwigSetup;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

class TwigSetupTest extends \PHPUnit_Framework_TestCase
{

    public function testRegisterTwigExtensions()
    {
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $twigSetup = new TwigSetup();
        $twigSetup->registerTwigExtensions($app);
        $filter = $app['twig']->getFilter('crudlex_arrayColumn');
        $this->assertNotNull($filter);

        $read = call_user_func($filter->getCallable(), [['id' => 1], ['id' => 2], ['id' => 3]], 'id');
        $expected = [1, 2, 3];
        $this->assertSame($expected, $read);

        $filter = $app['twig']->getFilter('crudlex_basename');

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

}
