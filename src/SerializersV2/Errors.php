<?php

namespace PhilKra\SerializersV2;

use PhilKra\Stores\ErrorsStore;
use PhilKra\Helper\Config;

/**
 *
 * Convert the Registered Errors to V2 JSON Schema
 *
 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
 *
 */
class Errors implements \JsonSerializable
{
    /**
     * @var \PhilKra\Stores\ErrorsStore
     */
    private $store;

    /**
     * @param ErrorsStore $store
     */
    public function __construct(Config $config, ErrorsStore $store)
    {
        $this->config = $config;
        $this->store = $store;
    }

    /**
     * Serialize Error Data to JSON "ready" Array
     *
     * @return array
     */
    public function jsonSerialize()
    {

        return $this->store->jsonSerialize();

    }
}
