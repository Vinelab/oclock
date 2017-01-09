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

use Illuminate\Contracts\Support\Arrayable;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
class Source implements Arrayable
{
    /**
     * The source id.
     *
     * @var string
     */
    private $id;

    /**
     * The source name.
     *
     * @var string
     */
    private $name;

    public function __construct($id, $name)
    {
        $id = ($id) ? $id : $this->generateId();
        $name = ($name) ? $name : $this->appName();

        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Make a new instance of this source class.
     *
     * @param string $id
     * @param string $name
     *
     * @return \OClock\Source
     */
    public static function make($id = null, $name = null)
    {
        return new static($id, $name);
    }

    /**
     * Get the unique identifier of this source.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the name of this source.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the array representation of this object.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * Generate the identifier for the source of this schedule.
     *
     * @return string
     */
    private function generateId()
    {
        return md5(config('app.key'));
    }

    /**
     * Get the configured application name.
     *
     * @return string
     */
    private function appName()
    {
        return config('app.name');
    }
}
