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

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class OClock
{
    private $store;

    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Get the list of schedules stored.
     *
     * @return \Illuminate\Support\Collection of \OClock\Schedule objects
     */
    public function schedules()
    {
        return $this->store->all();
    }
}
