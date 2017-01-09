<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Storage;

use OClock\Session;
use OClock\Schedule;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
interface StoreInterface
{
    /**
     * Save the given schedule.
     *
     * @param \OClock\Schedule $schedule
     *
     * @return bool Determines whether the save was successful.
     */
    public function save(Schedule $schedule);

    /**
     * Get all the stored schedules.
     *
     * @return \Illuminate\Support\Collection
     */
    public function schedules();

    /**
     * Get all the sessions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function sessions();

    /**
     * Get all the sessions grouped by day
     *
     * @return \Illuminate\Support\Collection
     */
    public function sessionsByDay();

    /**
     * Start a new session in the store
     * by creating a new session record
     * with no "finished_at" field.
     *
     * @param \OClock\Session $session
     *
     * @return bool
     */
    public function startSession(Session $session);

    /**
     * Finish the given session.
     * - Sets the "finished_at" field to the current date
     * - Marks the session as no longer running
     * - Adds the output to the session.
     *
     * @param \OClock\Session $session
     *
     * @return bool
     */
    public function finishSession(Session $session, $output = '');
}
