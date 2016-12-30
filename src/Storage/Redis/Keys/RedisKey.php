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
class RedisKey
{
    /**
     * This will hold the original key
     * to be used for resetting.
     *
     * @var string
     */
    private $original = '';

    /**
     * The string representation of the key for this instance.
     *
     * @var string
     */
    protected $key = '';

    public function __construct($id = null)
    {
        $this->original = $this->key;

        if (!is_null($id) && !empty($id)) {
            $this->add($id);
        }
    }

    /**
     * Make a new key.
     *
     * @param string|int $id
     *
     * @return self
     */
    public static function make($id = null)
    {
        return new static($id);
    }

    /**
     * Append a key to the key of this instance.
     *
     * @param string $key
     *
     * @return self
     */
    public function add($key)
    {
        // when this is the first key we're adding
        // we won't append to an empty one to avoid
        // dingling ":" at the beginning.
        if (!isset($this->key) || empty($this->key)) {
            $this->key = $key;
        } else {
            $this->key = $this->key.":$key";
        }

        return $this;
    }

    public function set($key)
    {
        return $this->reset()->add($key);
    }

    /**
     * Reset the key of this instance.
     *
     * @return self
     */
    public function reset()
    {
        $this->key = $this->original;

        return $this;
    }

    public function __toString()
    {
        return $this->key;
    }
}
