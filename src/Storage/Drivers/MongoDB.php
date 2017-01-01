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

use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Connection;
use OClock\Schedule;
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

    const COLLECTION = 'schedules';

    public function __construct(Connection $connection)
    {
        $this->db = $connection;
    }

    public function save(Schedule $schedule)
    {
        $this->db->collection(self::COLLECTION)
            ->where(['source_id' => $schedule->sourceId()])
            ->update($schedule->toArray(), ['upsert' => true]);
    }

    public function all()
    {
        $schedules = $this->db->collection(self::COLLECTION)->get();

        return $schedules->map(function ($schedule) {
            return Schedule::makeWithMetadata($schedule['source_id'],
                $schedule['source_name'],
                Collection::make($schedule['events']),
                $schedule['created_at']
            );
        });
    }
}
