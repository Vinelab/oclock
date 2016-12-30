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

use OClock\Commands\Report;
use OClock\Storage\Drivers\Redis;
use OClock\Storage\StoreInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Redis as RedisClient;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class OClockServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // register command
        if ($this->app->runningInConsole()) {
            $this->commands([
                Report::class,
            ]);
        }

        $this->app->bind(StoreInterface::class, function () {
            return new Redis(RedisClient::connection());
        });

        // schedule reporting event
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('oclock:report')
                     ->description('O\'Clock schedule report')
                     ->everyMinute();
        });
    }

    public function register()
    {
    }
}
