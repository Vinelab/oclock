<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock;

use OClock\Tests\ScheduleTest;

function config($parameter)
{
    ScheduleTest::$functions->config($parameter);
}

namespace OClock\Tests;

use DateTime;
use Mockery as M;
use OClock\Event;
use OClock\Source;
use OClock\Schedule;
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Collection;
use Illuminate\Console\Scheduling\Event as ScheduleEvent;
use Illuminate\Console\Scheduling\Schedule as IlluminateSchedule;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class ScheduleTest extends PHPUnit_Framework_TestCase
{
    public static $functions;

    public function setUp()
    {
        parent::setUp();

        // $this->appKey = '123';
        // $this->sourceId = md5($this->appKey);
        // $this->sourceName = 'The Test App';

        self::$functions = M::mock();
        self::$functions->shouldReceive('config');

        // left out for it doesn't work.

        // self::$functions->shouldReceive('config')->once()->with('app.key')
        //     ->andReturn($this->appKey);
        // self::$functions->shouldReceive('config')->once()->with('app.name')
        //     ->andReturn($this->sourceName);
    }

    public function test_making_with_metadata_and_date_as_string()
    {
        $createdAt = '2016-12-30 00:00:00';

        $mSource = M::mock(Source::class);

        $schedule = Schedule::makeWithMetadata($mSource, new Collection(), $createdAt);

        $this->assertEquals($mSource, $schedule->source());
        $this->assertInstanceOf(DateTime::class, $schedule->createdAt());
        $this->assertEquals($createdAt, $schedule->createdAt()->format('Y-m-d H:i:s'));
    }

    public function test_making_with_metadata_and_date_as_datetime()
    {
        $createdAt = new DateTime('2016-12-30 00:00:00');

        $mSource = M::mock(Source::class);

        $schedule = Schedule::makeWithMetadata($mSource, new Collection(), $createdAt);

        $this->assertEquals($mSource, $schedule->source());
        $this->assertInstanceOf(DateTime::class, $schedule->createdAt());
        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $schedule->createdAt()->format('Y-m-d H:i:s'));
    }

    public function test_making_schedule_with_schedule()
    {
        $mSchedule = M::mock(IlluminateSchedule::class);
        $mEvent1 = M::mock(ScheduleEvent::class);
        $mEvent1->shouldReceive('getExpression')->once()->andReturn('* * * * *')
            ->shouldReceive('getSummaryForDisplay')->once()->andReturn('Event 1')
            ->shouldReceive('buildCommand')->once()->andReturn('php artisan tla3minrase');
        $mEvent2 = M::mock(ScheduleEvent::class);
        $mEvent2->shouldReceive('getExpression')->once()->andReturn('0 0 * * *')
            ->shouldReceive('getSummaryForDisplay')->once()->andReturn('Event 2')
            ->shouldReceive('buildCommand')->once()->andReturn('php artisan kamen');

        $events = [$mEvent1, $mEvent2];
        $mSchedule->shouldReceive('events')->once()->andReturn($events);

        $schedule = Schedule::make($mSchedule);

        $this->assertInstanceOf(Schedule::class, $schedule);
        $expectedEvents = new Collection([Event::make($mEvent1), Event::make($mEvent2)]);
        $this->assertEquals($expectedEvents, $schedule->events());
        $expectedSource = new Source(md5(null), null);
        $this->assertEquals($expectedSource, $schedule->source());
    }
}
