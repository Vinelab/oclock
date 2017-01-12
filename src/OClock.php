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

use Request;
use Illuminate\Support\Collection;
use OClock\Storage\StoreInterface;
use Illuminate\Console\Scheduling\Schedule as IlluminateSchedule;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class OClock
{
    /**
     * The data store repository.
     *
     * @var \OClock\StoreInterface
     */
    private $store;

    /**
     * Stores the sessions for each event.
     * Sessions are added here in the form of [event_id => Session]
     * at the "before" hook, and then used by the "after" to
     * finish and close the sessions.
     *
     * @var array
     */
    private $eventSessions = [];

    public function __construct(StoreInterface $store = null)
    {
        $this->store = $store;
    }

    /**
     * Register the given schedule by parsing and storing in the database.
     *
     * @param \Illuminate\Console\Schedule $schedule
     *
     * @return bool Determines whether the schedules was stored successfully
     */
    public function register(IlluminateSchedule $schedule)
    {
        // only register when running in console
        // and it's the schedule:run command
        if (!in_array('schedule:run', Request::server('argv'))) {
            return;
        }

        // iterate the schedule's events and:
        // 1. Set the output to be sent to the event's log file
        // 2. hook into the before and after session
        $events = Collection::make($schedule->events())->each(function ($scheduledEvent) {
            $event = Event::make($scheduledEvent);
            $scheduledEvent->sendOutputTo($event->outputFilePath())
                ->before(function () use ($event) {
                    $this->beforeSession($event);
                })
                ->after(function () use ($event) {
                    $this->afterSession($event);
                });
        });

        // store the list of events with the source information
        return $this->store->save(Schedule::make($schedule));
    }

    /**
     * Get the list of schedules stored.
     *
     * @return \Illuminate\Support\Collection of \OClock\Schedule objects
     */
    public function schedules()
    {
        return $this->store->schedules();
    }

    public function sessions()
    {
        return $this->store->sessions();
    }

    public function sessionsByDay()
    {
        return $this->store->sessionsByDay();
    }

    public function sessionsForEvent($eventId)
    {
        return $this->store->sessionsForEvent($eventId);
    }

    /**
     * Called before running an event (a session).
     *
     * @param \OClock\Event $event
     */
    private function beforeSession(Event $event)
    {
        $source = Source::make();
        // store session
        $session = Session::make($source, $event);
        $this->store->startSession($session);
        // add session to the event sessions array
        $this->addEventSession($event, $session);
    }

    /**
     * Called after finishing a session.
     *
     * @param \OClock\Event $event
     */
    private function afterSession(Event $event)
    {
        $session = $this->getEventSession($event);

        // get output file contents and store them in store in the event's session
        $output = '';
        if (file_exists($event->outputFilePath())) {
            $output = file_get_contents($event->outputFilePath());
            unlink($event->outputFilePath());
        }

        // update the "finished_at" field of the session, now that we're done.
        $this->store->finishSession($session, $output);

        // remove session from event sessions array
        $this->removeEventSession($event);
    }

    /**
     * Add an event session to the array of event sessions.
     *
     * @param \OClock\Event   $event
     * @param \OClock\Session $session
     */
    private function addEventSession(Event $event, Session $session)
    {
        $this->eventSessions[$event->id()] = $session;
    }

    /**
     * Get the session for the given event.
     *
     * @param \OClock\Event $event
     *
     * @return \OClock\Session
     */
    private function getEventSession(Event $event)
    {
        if (isset($this->eventSessions[$event->id()])) {
            return $this->eventSessions[$event->id()];
        }
    }

    /**
     * Remove the session of the given event.
     *
     * @param \OClock\Event $event
     */
    private function removeEventSession(Event $event)
    {
        unset($this->eventSessions[$event->id()]);
    }
}
