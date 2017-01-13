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

use OClock\Event;
use OClock\Source;
use OClock\Session;
use OClock\Schedule;
use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Connection;
use OClock\Storage\StoreInterface;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class MongoDB implements StoreInterface
{
    /**
     * The database connection.
     *
     * @var \Jenssegers\Mongodb\Connection
     */
    private $db;

    const SCHEDULES_COLLECTION = 'schedules';
    const SESSIONS_COLLECTION = 'schedules_sessions';

    public function __construct(Connection $connection)
    {
        $this->db = $connection;
    }

    public function save(Schedule $schedule)
    {
        return $this->db->collection(self::SCHEDULES_COLLECTION)
            ->where(['source.id' => $schedule->source()->id()])
            ->update($schedule->toArray(), ['upsert' => true]);
    }

    public function schedules()
    {
        $schedules = $this->db->collection(self::SCHEDULES_COLLECTION)->get();

        return $schedules->map(function ($schedule) {
            $events = Collection::make($schedule['events'])->map(function ($event) {
                return Event::makeWithData($event);
            });

            return Schedule::makeWithMetadata(
                Source::make($schedule['source']['id'], $schedule['source']['name']),
                $events,
                $schedule['created_at']
            );
        });
    }

    public function sessions()
    {
        $sessions = $this->db->collection(self::SESSIONS_COLLECTION)->take(100)->get();

        return $this->mapSessions($sessions);
    }

    public function sessionsByDay()
    {
        $now = date('Y-m-d H:i:s');
        $yesterday = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($now)));
        // get today's and yesterday's sessions
        $sessions = $this->db->collection(self::SESSIONS_COLLECTION)
            ->where('created_at', '<', $now)
            ->where('created_at', '>', $yesterday)
            ->get();

        $sessions = $this->mapSessions($sessions);

        $days = [];
        foreach ($sessions as $session) {
            $day = $session->createdAt()->format('d-m-Y');
            $days[$day][] = $session;
        }

        return new Collection($days);
    }

    public function sessionsForEvent($eventId)
    {
        $sessions = $this->db->collection(self::SESSIONS_COLLECTION)->where('event.id', $eventId)->get();

        return $this->mapSessions($sessions);
    }

    public function startSession(Session $session)
    {
        return $this->db->collection(self::SESSIONS_COLLECTION)->insert($session->toArray());
    }

    public function finishSession(Session $session, $output = '')
    {
        return $this->db->collection(self::SESSIONS_COLLECTION)
            ->where('id', $session->id())
            ->update([
                'output' => $output,
                'is_running' => false,
                'finished_at' => date('Y-m-d H:i:s'),
            ]);
    }

    private function mapSessions(Collection $sessions)
    {
        return $sessions->map(function ($session) {
            return Session::make(
                Source::make($session['source']['id'], $session['source']['name']),
                Event::makeWithData($session['event']),
                $session['is_running'],
                $session['id'],
                $session['created_at'],
                $session['finished_at'],
                $session['output']
            );
        });
    }
}
