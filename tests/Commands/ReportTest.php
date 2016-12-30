<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Tests\Commands;

use Mockery as M;
use OClock\Commands\Report;
use PHPUnit_Framework_TestCase;
use OClock\Storage\StoreInterface;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\CallbackEvent;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class ReportTest extends PHPUnit_Framework_TestCase
{
    public function test_reporting()
    {
        $this->markTestIncomplete();
        $report = new Report();
        $mSchedule = M::mock(Schedule::class);

        $mEvent1 = M::mock(CallbackEvent::class);
        $mEvent1->shouldReceive('getExpression')->andReturn('* * * * *')
            ->shouldReceive('getSummaryForDisplay')->andReturn('Some First Service');

        $mEvent2 = M::mock(CallbackEvent::class);
        $mEvent2->shouldReceive('getExpression')->andReturn('10 * * * *')
            ->shouldReceive('getSummaryForDisplay')->andReturn('Another Service');

        $events = [$mEvent1, $mEvent2];
        $mSchedule->shouldReceive('events')->once()->andReturn($events);

        $mStore = M::mock(StoreInterface::class);

        $report->handle($mSchedule, $mStore);
    }
}
