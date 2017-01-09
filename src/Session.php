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
use Illuminate\Contracts\Support\Arrayable;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Session implements Arrayable
{
    /**
     * The session id.
     *
     * @var string
     */
    private $id;

    /**
     * The event that this session is associated with.
     *
     * @var \OClock\Event
     */
    private $event;

    /**
     * The source/app.
     *
     * @var \OClock\Source
     */
    private $source;

    /**
     * Specifies the running state of the session.
     *
     * @var bool
     */
    private $isRunning;

    /**
     * The date this session was created.
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * The date this session has ended.
     *
     * @var \DateTime
     */
    private $finishedAt;

    /**
     * The output from this session.
     *
     * @var string
     */
    private $output;

    public function __construct(
        Source $source,
        Event $event,
        $isRunning = true,
        $id = null,
        $createdAt = null,
        $finishedAt = null,
        $output = ''
    ) {
        $this->event = $event;
        $this->source = $source;
        $this->isRunning = $isRunning;
        $this->finishedAt = $this->dateTime($finishedAt);
        $this->createdAt = $this->dateTime($createdAt);
        $this->output = $output;

        $this->id = ($id) ? $id : $this->generateId();
    }

    /**
     * Make a new instance of this session class.
     *
     * @return \OClock\Session
     */
    public static function make(
        Source $source,
        Event $event,
        $isRunning = true,
        $id = null,
        $createdAt = null,
        $finishedAt = null,
        $output = ''
    ) {
        return new static($source, $event, $isRunning, $id, $createdAt, $finishedAt, $output);
    }

    /**
     * The unique identifier of this session.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the date this session was created at.
     *
     * @return \DateTime
     */
    public function createdAt()
    {
        return $this->createdAt;
    }

    /**
     * Get the array representation of this session.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id(),
            'source' => $this->source->toArray(),
            'event' => $this->event->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'finished_at' => $this->finishedAt ? $this->finishedAt->format('Y-m-d H:i:s') : null,
            'is_running' => $this->isRunning,
            'output' => $this->output,
        ];
    }

    /**
     * Turn the given date into a DateTime instance.
     *
     * @param string|DateTime $date
     *
     * @return \DateTime
     */
    private function dateTime($date)
    {
        if ($date && !$date instanceof DateTime && is_string($date)) {
            $date = new DateTime($date);
        }

        return ($date) ? $date : new DateTime();
    }

    /**
     * Generate a unique identifier for this session.
     *
     * @return string
     */
    private function generateId()
    {
        return md5(uniqid());
    }
}
