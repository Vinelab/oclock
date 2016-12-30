<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Storage\Drivers;

use OClock\Schedule;
use Predis\Client as RedisClient;
use OClock\Storage\StoreInterface;
use Illuminate\Support\Collection;
use OClock\Storage\Redis\Keys\EventKey;
use OClock\Storage\Redis\Keys\ScheduleKey;
use OClock\Storage\Redis\Keys\RedisKeysManager;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Redis implements StoreInterface
{
    /**
     * The Redis client.
     *
     * @var
     */
    private $client;

    public function __construct(RedisClient $client)
    {
        $this->client = $client;
    }

    /**
     * @{inheritdoc} See StoreInterface
     */
    public function save(Schedule $schedule)
    {
        $pipe = $this->client->pipeline();
        // add the id to the list of "schedules"
        $pipe->sadd($this->makeKey(ScheduleKey::make()), $schedule->sourceId());
        // add the schedule metadata to the "schedules:{id}" hash
        $pipe->hmset($this->makeKey(ScheduleKey::make($schedule->sourceId())), $schedule->metadata());
        foreach ($schedule->events() as $event) {
            // add the list of events of the given schedule "schedules:{id}:events"
            $pipe->sadd($this->makeKey(ScheduleKey::make($schedule->sourceId()), EventKey::make()), $event->id());
            // add the event data to the event's hash "schedules:{id}:events:{event id}"
            $pipe->hmset(
                $this->makeKey(ScheduleKey::make($schedule->sourceId()), EventKey::make($event->id())),
                $event->toArray()
            );
        }

        return $pipe->execute();
    }

    /**
     * @{inheritdoc} See StoreInterface
     */
    public function all()
    {
        $pipe = $this->client->pipeline();
        // read the list of schedules to get their ids
        $sourcesIds = $this->client->smembers($this->makeKey(ScheduleKey::make()));
        foreach ($sourcesIds as $sourceId) {
            // get the source's schedule metadata
            $pipe->hgetall($this->makeKey(ScheduleKey::make($sourceId)));
            $pipe->smembers($this->makeKey(ScheduleKey::make($sourceId), EventKey::make()));
        }

        $sourcesAndEventsIds = $pipe->execute();

        // structure and merge sources with their corresponding events ids
        $sources = [];
        foreach ($sourcesAndEventsIds as $key => $sourceOrEventIds) {
            if ($key % 2 == 0) {
                // odd indices are source metadata
                $sources[] = $sourceOrEventIds;
            } else {
                // even indices are event ids of the previous source
                $sources[$key - 1]['events'] = $sourceOrEventIds;
            }
        }

        $pipe = $this->client->pipeline();
        foreach ($sources as $key => $source) {
            foreach ($source['events'] as $eventId) {
                $pipe->hgetall($this->makeKey(ScheduleKey::make($source['source_id']), EventKey::make($eventId)));
            }
            $source['events'] = $pipe->execute();

            $schedule = Schedule::makeWithMetadata(
                $source['source_id'],
                $source['source_name'],
                Collection::make($source['events']),
                $source['created_at']
            );
            $sources[$key] = $schedule;
        }

        return Collection::make($sources);
    }

    /**
     * Make a redis key from the given key args.
     * NOTE: Takes an infinite number of keys and merges them together
     *     adding the prefix.
     *
     * @return string
     */
    protected function makeKey()
    {
        $manager = new RedisKeysManager();

        return call_user_func_array([$manager, 'makeKey'], func_get_args());
    }
}
