<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Tests;

use Mockery as M;
use OClock\Event;
use PHPUnit_Framework_TestCase;
use Illuminate\Console\Scheduling\Event as ScheduleEvent;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class EventTest extends PHPUnit_Framework_TestCase
{
    public function test_making_event_instnace()
    {
        $expression = '* * * * *';
        $description = 'The description of the event to be displayed';
        $command = 'php artisan kolkhara';
        $id = md5($expression.$description);

        $mEvent = M::mock(ScheduleEvent::class);
        $mEvent->shouldReceive('getExpression')->once()->andReturn($expression)
            ->shouldReceive('getSummaryForDisplay')->once()->andReturn($description)
            ->shouldReceive('buildCommand')->once()->andReturn($command);

        $event = Event::make($mEvent);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals($id, $event->id());
        $this->assertEquals([
            'expression' => $expression,
            'description' => $description,
            'id' => $id,
            'command' => $command,
        ], $event->toArray());
    }

    public function test_making_event_with_given_data()
    {
        $expression = '* * * * *';
        $description = 'The description of the event to be displayed';
        $command = 'php artisan kolkhara';
        $id = md5($expression.$description);

        $data = compact('id', 'command', 'description', 'expression');

        $event = Event::makeWithData($data);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals($id, $event->id());
        $this->assertEquals([
            'id' => $id,
            'command' => $command,
            'description' => $description,
            'expression' => $expression,
        ], $event->toArray());
    }
}
