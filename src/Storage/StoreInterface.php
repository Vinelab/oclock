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
    public function all();
}
