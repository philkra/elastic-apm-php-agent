<?php

namespace PhilKra\SerializersV2;

use PhilKra\Stores\TransactionsStore;
use PhilKra\Helper\Config;

/**
 *
 * Convert the Registered Transactions to V2 JSON Schema
 *
 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
 *
 */
class Transactions implements \JsonSerializable
{
    /**
     * @var \PhilKra\Stores\TransactionsStore
     */
    private $store;

    /**
     * Transactions constructor.
     * @param Config $config
     * @param TransactionsStore $store
     */
    public function __construct(Config $config, TransactionsStore $store)
    {
        $this->store = $store;
    }

    /**
     * Serialize Transactions Data to JSON "ready" Array
     *
     * @return array
     */
    public function jsonSerialize()
    {

        return $this->store->jsonSerialize();

    }
}
