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

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Console\Scheduling\Schedule as IlluminateSchedule;

/**
 * A schedule containing a series of events with their metadata.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Schedule implements Arrayable
{
    /**
     * The collection of events.
     *
     * @var \Illuminate\Support\Collection of \OClock\Event objects.
     */
    private $events;

    /**
     * The date when this schedule was first created.
     *
     * @var DateTime
     */
    private $createdAt;

    /**
     * The source.
     *
     * @var \OClock\Source
     */
    private $source;

    public function __construct(Source $source, Collection $events, DateTime $createdAt = null)
    {
        $this->source = $source;
        $this->events = $events;
        $this->createdAt = ($createdAt) ? $createdAt : new DateTime();
    }

    /**
     * Make a new instance of this schedule class from the given Illuminate schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return \OClock\Schedule
     */
    public static function make(IlluminateSchedule $schedule)
    {
        $events = Collection::make($schedule->events())->map(function ($event) {
            return Event::make($event);
        });

        return new static(Source::make(), $events);
    }

    /**
     * Make a new instance of this schedule class with the given collection of events.
     *
     * @param \Illuminate\Support\Collection $events
     *
     * @return \OClock\Schedule
     */
    public static function makeWithEvents(Source $source, Collection $events)
    {
        return new static($source, $events);
    }

    /**
     * Make a Schedule instance with existing metadata.
     * Used mostly when fetching records from DB.
     *
     * @param string $source
     * @param array  $events
     * @param string $createdAt
     *
     * @return self
     */
    public static function makeWithMetadata($source, $events, $createdAt)
    {
        if (!$createdAt instanceof DateTime && is_string($createdAt)) {
            $createdAt = new DateTime($createdAt);
        }

        return new static($source, $events, $createdAt);
    }

    /**
     * Get the source.
     *
     * @return \OClock\Source
     */
    public function source()
    {
        return $this->source;
    }

    /**
     * Get the date when this schedule was first created.
     *
     * @return DateTime
     */
    public function createdAt()
    {
        return $this->createdAt;
    }

    /**
     * Get the schedule being scheduleed.
     *
     * @return \Illuminate\Support\Collection of \OClock\Event objects
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * Get the array representation of this schedule.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'source' => $this->metadata(),
            'events' => $this->events()->toArray(),
            'created_at' => $this->createdAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get only the metadata of this schedule (without events).
     *
     * @return array
     */
    public function metadata()
    {
        return $this->source()->toArray();
    }
}
