<?php

/*
 * This file is part of the oclock project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OClock\Storage\Redis\Keys;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class EventKey extends RedisKey
{
    protected $key = 'events';
}
