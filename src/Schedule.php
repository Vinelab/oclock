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

/**
 * A schedule containing a series of events with their metadata.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Schedule
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
     * This schedule's source id.
     *
     * @var string
     */
    private $sourceId;

    /**
     * This schedule's source name.
     *
     * @var string
     */
    private $sourceName;

    public function __construct(Collection $events, $sourceId = null, $sourceName = null, DateTime $createdAt = null)
    {
        $this->events = $events;
        $this->createdAt = ($createdAt) ? $createdAt : new DateTime();
        $this->sourceName = ($sourceName) ? $sourceName : $this->appName();
        $this->sourceId = ($sourceId) ? $sourceId : $this->generateSourceId();
    }

    /**
     * Make a new instance of this schedule class.
     *
     * @param \Illuminate\Support\Collection $events
     *
     * @return \OClock\schedule
     */
    public static function make(Collection $events)
    {
        return new static($events);
    }

    /**
     * Make a Schedule instance with existing metadata.
     * Used mostly when fetching records from DB.
     *
     * @param  string $sourceId
     * @param  string $sourceName
     * @param  array $events
     * @param  string $createdAt
     *
     * @return self
     */
    public static function makeWithMetadata($sourceId, $sourceName, $events, $createdAt)
    {
        if (!$createdAt instanceof DateTime && is_string($createdAt)) {
            $createdAt = new DateTime($createdAt);
        }

        return new static($events, $sourceId, $sourceName, $createdAt);
    }

    /**
     * Generate the identifier for the source of this schedule.
     *
     * @return string
     */
    private function generateSourceId()
    {
        return md5(config('app.key'));
    }

    /**
     * Get the configured application name.
     *
     * @return string
     */
    private function appName()
    {
        return config('app.name');
    }

    /**
     * Get the id of this schedule's source.
     *
     * @return string
     */
    public function sourceId()
    {
        return $this->sourceId;
    }

    /**
     * Get the name of this schedule's source.
     *
     * @return string
     */
    public function sourceName()
    {
        return $this->sourceName;
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
        return array_merge($this->metadata(), ['events' => $this->events()->toArray()]);
    }

    /**
     * Get only the metadata of this schedule (without events).
     *
     * @return array
     */
    public function metadata()
    {
        return [
            'source_id' => $this->sourceId(),
            'source_name' => $this->sourceName(),
            'created_at' => $this->createdAt()->format('Y-m-d H:i:s'),
        ];
    }
}
