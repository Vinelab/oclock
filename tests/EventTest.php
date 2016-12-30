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

        $mEvent = M::mock(ScheduleEvent::class);
        $mEvent->shouldReceive('getExpression')->once()->andReturn($expression)
            ->shouldReceive('getSummaryForDisplay')->once()->andReturn($description);

        $event = Event::make($mEvent);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals(true, $event->isDue());
        $this->assertEquals(md5($expression.$description), $event->id());
        $this->assertEquals(date('Y-m-d H:i', strtotime('-1 minutes')), $event->lastRunDate()->format('Y-m-d H:i'));
        $this->assertEquals(date('Y-m-d H:i', strtotime('+1 minutes')), $event->nextRunDate()->format('Y-m-d H:i'));
        $this->assertEquals([
            'expression' => $expression,
            'description' => $description,
            'is_due' => true,
            'last_run_at' => date('Y-m-d H:i', strtotime('-1 minutes')),
            'next_run_at' => date('Y-m-d H:i', strtotime('+1 minutes')),
        ], $event->toArray());
    }
}
