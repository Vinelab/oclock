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

use Cron\CronExpression;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Console\Scheduling\Event as ScheduleEvent;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Event implements Arrayable
{
    /**
     * The unique identifier of this event.
     *
     * @var string
     */
    private $id;

    /**
     * Determines whether the event is due or not.
     *
     * @var bool
     */
    private $isDue;

    /**
     * The Cron expression.
     *
     * @var string
     */
    private $expression;

    /**
     * The event's description.
     *
     * @var string
     */
    private $description;

    /**
     * The date when this event has run last.
     *
     * @var DateTime
     */
    private $lastRunDate;

    /**
     * The date when this event will run again.
     *
     * @var DateTime
     */
    private $nextRunDate;

    public function __construct($expression, $description)
    {
        $this->expression = $expression;
        $this->description = $description;

        $cron = CronExpression::factory($expression);
        $this->isDue = (bool) $cron->isDue();
        $this->lastRunDate = $cron->getPreviousRunDate();
        $this->nextRunDate = $cron->getNextRunDate();

        $this->id = $this->generateId();
    }

    /**
     * Get a new instance of this event class.
     *
     * @param ScheduleEvent $event
     *
     * @return \OClock\Event
     */
    public static function make(ScheduleEvent $event)
    {
        return new static($event->getExpression(), $event->getSummaryForDisplay());
    }

    /**
     * Get the unique identifier of this event.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the Cron expression of this event.
     *
     * @return string
     */
    public function expression()
    {
        return $this->expression;
    }

    /**
     * Get the description of this event.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Determine whether this event is due or not.
     *
     * @return bool
     */
    public function isDue()
    {
        return $this->isDue;
    }

    /**
     * Get the time when this event was run last.
     *
     * @return DateTime
     */
    public function lastRunDate()
    {
        return $this->lastRunDate;
    }

    /**
     * Get the time when this event will run next.
     *
     * @return DateTime
     */
    public function nextRunDate()
    {
        return $this->nextRunDate;
    }

    /**
     * Get the array representation of this event.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'expression' => $this->expression(),
            'description' => $this->description(),
            'is_due' => $this->isDue(),
            'last_run_at' => $this->lastRunDate()->format('Y-m-d H:i'),
            'next_run_at' => $this->nextRunDate()->format('Y-m-d H:i'),
        ];
    }

    /**
     * Generate a random unique identifier for this event.
     *
     * @return string
     */
    private function generateId()
    {
        return md5($this->expression().$this->description());
    }
}
