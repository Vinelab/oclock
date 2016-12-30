<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Tests\Storage\Drivers;

use Mockery as M;
use Illuminate\Support\Collection;
use OClock\Event;
use OClock\Schedule;
use OClock\Storage\Drivers\Redis;
use PHPUnit_Framework_TestCase;
use Predis\Client;
use Predis\Pipeline\Pipeline;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class RedisTest extends PHPUnit_Framework_TestCase
{
    public function test_saving()
    {
        $mClient = M::mock(Client::class);
        $mPipe = M::mock(Pipeline::class);

        $redis = new Redis($mClient);

        $mClient->shouldReceive('pipeline')->once()->andReturn($mPipe);

        $mEvent1 = M::mock(Event::class);
        $mEvent1->shouldReceive('id')->twice()->andReturn('id1')
            ->shouldReceive('toArray')->once()->andReturn(['id' => 'id1', 'description' => 'something']);

        $mEvent2 = M::mock(Event::clasS);
        $mEvent2->shouldReceive('id')->twice()->andReturn('id2')
            ->shouldReceive('toArray')->once()->andReturn(['id' => 'id2', 'description' => 'another thing']);

        $sourceId = 'source-id';
        $sourceName = 'source-name';
        $createdAt = '2016-12-30 00:00:00';

        $mPipe->shouldReceive('sadd')->once()->with('oclock:schedules', $sourceId);
        $mPipe->shouldReceive('hmset')->once()->with("oclock:schedules:$sourceId", [
            'source_id' => $sourceId,
            'source_name' => $sourceName,
            'created_at' => $createdAt,
        ]);

        $mPipe->shouldReceive('sadd')->once()->with("oclock:schedules:$sourceId:events", $mEvent1->id());
        $mPipe->shouldReceive('sadd')
            ->once()->with("oclock:schedules:$sourceId:events", $mEvent2->id());

        $mPipe->shouldReceive('hmset')->once()->with("oclock:schedules:$sourceId:events:".$mEvent1->id(), $mEvent1->toArray());
        $mPipe->shouldReceive('hmset')
            ->once()->with("oclock:schedules:$sourceId:events:".$mEvent2->id(), $mEvent2->toArray());

        $mPipe->shouldReceive('execute')->once()->andReturn(true);

        $events = Collection::make([$mEvent1, $mEvent2]);

        $result = $redis->save(Schedule::makeWithMetadata($sourceId, $sourceName, $events, $createdAt));

        $this->assertTrue($result);
    }
}
