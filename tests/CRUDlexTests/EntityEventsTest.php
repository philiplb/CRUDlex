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

use CRUDlex\EntityEvents;
use CRUDlexTestEnv\TestDBSetup;

class EntityEventsTest extends \PHPUnit_Framework_TestCase
{

    protected $entity;

    protected function setUp()
    {
        $crudServiceProvider = TestDBSetup::createServiceProvider();
        $dataBook = $crudServiceProvider->getData('book');
        $this->entity = $dataBook->createEmpty();
    }

    public function testPushPopEvent()
    {
        $function = function() {
            return true;
        };
        $events = new EntityEvents();
        $events->pushEvent('before', 'create', $function);
        $read = $events->popEvent('before', 'create');
        $this->assertSame($function, $read);

        $read = $events->popEvent('before', 'create');
        $this->assertNull($read);

        $read = $events->popEvent('before', 'update');
        $this->assertNull($read);
    }

    public function testShouldExecuteEvents()
    {
        $functionTrue = function() {
            return true;
        };
        $functionFalse = function() {
            return false;
        };

        $events = new EntityEvents();
        $result = $events->shouldExecuteEvents($this->entity, 'invalidMoment', 'invalidAction');
        $this->assertTrue($result);

        $events->pushEvent('moment', 'action', $functionTrue);
        $result = $events->shouldExecuteEvents($this->entity, 'moment', 'action');
        $this->assertTrue($result);
        $result = $events->shouldExecuteEvents($this->entity, 'invalidMoment', 'invalidAction');
        $this->assertTrue($result);

        $events->pushEvent('moment', 'action', $functionFalse);
        $result = $events->shouldExecuteEvents($this->entity, 'moment', 'action');
        $this->assertFalse($result);

    }

}
