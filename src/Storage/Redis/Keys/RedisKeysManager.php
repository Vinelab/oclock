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
 * RedisKeysManager.
 * This RedisKeysManager wont include the *Key that have a null id.
 * The purpose behind this modification is to remove any undesired Key if no id is provided, like in the specific case
 * of building a key with no talentId.
 *
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class RedisKeysManager
{
    const PREFIX = 'oclock:';

    public function makeKey()
    {
        $args = func_get_args();

        $key = $args[0];
        unset($args[0]);

        foreach ($args as $arg) {
            $key->add($arg);
        }

        return (string) self::PREFIX.$key;
    }
}
