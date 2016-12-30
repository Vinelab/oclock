<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule as IlluminateScheduler;
use Illuminate\Support\Collection;
use OClock\Event;
use OClock\Schedule;
use OClock\Storage\StoreInterface;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oclock:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report scheduled events.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(IlluminateScheduler $scheduler, StoreInterface $store)
    {
        $events = Collection::make($scheduler->events())
            ->map(function ($event) { return Event::make($event); });

        $schedule = Schedule::make($events);

        $store->save($schedule);
    }
}
