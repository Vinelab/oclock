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

use DateTime;
use OClock\Schedule;
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Collection;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class ScheduleTest extends PHPUnit_Framework_TestCase
{
    public function test_making_with_metadata_and_date_as_string()
    {
        $sourceId = 'source-id';
        $sourceName = 'source-name';
        $createdAt = '2016-12-30 00:00:00';

        $schedule = Schedule::makeWithMetadata($sourceId, $sourceName, new Collection(), $createdAt);

        $this->assertEquals($sourceId, $schedule->sourceId());
        $this->assertEquals($sourceName, $schedule->sourceName());
        $this->assertInstanceOf(DateTime::class, $schedule->createdAt());
        $this->assertEquals($createdAt, $schedule->createdAt()->format('Y-m-d H:i:s'));
    }

    public function test_making_with_metadata_and_date_as_datetime()
    {
        $sourceId = 'source-id';
        $sourceName = 'source-name';
        $createdAt = new DateTime('2016-12-30 00:00:00');

        $schedule = Schedule::makeWithMetadata($sourceId, $sourceName, new Collection(), $createdAt);

        $this->assertEquals($sourceId, $schedule->sourceId());
        $this->assertEquals($sourceName, $schedule->sourceName());
        $this->assertInstanceOf(DateTime::class, $schedule->createdAt());
        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $schedule->createdAt()->format('Y-m-d H:i:s'));
    }

}
