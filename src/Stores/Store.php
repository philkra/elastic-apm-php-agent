<?php

namespace PhilKra\Stores;

use PhilKra\Events\EventBean;

/**
 *
 * Registry for captured the Events
 *
 */
class Store implements \JsonSerializable
{
    /**
     * Set of Events
     *
     * @var array of \PhilKra\Events\EventBean
     */
    protected $store = [];

    /**
     * Get all Registered Errors
     *
     * @return array of \PhilKra\Events\EventBean
     */
    public function list() : array
    {
        return $this->store;
    }

    /**
     * Is the Store Empty ?
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->store);
    }

    /**
     * Empty the Store
     *
     * @return void
     */
    public function reset()
    {
        $this->store = [];
    }

    /**
     * Serialize the Events Store
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->store;
    }
}
