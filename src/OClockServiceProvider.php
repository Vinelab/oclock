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

use DB;
use OClock\Storage\StoreInterface;
use OClock\Storage\Drivers\MongoDB;
use Illuminate\Support\ServiceProvider;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class OClockServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->app->bind(StoreInterface::class, function () {
            return new MongoDB(DB::connection());
        });

        $this->app['vinelab.oclock'] = $this->app->share(function ($app) {
            return $app->make(OClock::class);
        });

        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('OClock', 'OClock\Facade\OClock');
        });
    }
}
