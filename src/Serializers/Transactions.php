<?php

namespace PhilKra\Serializers;

use PhilKra\Exception\Serializers\UnsupportedApmVersionException;
use PhilKra\Stores\TransactionsStore;
use PhilKra\Helper\Config;

/**
 *
 * Convert the Registered Transactions to JSON Schema
 *
 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
 *
 */
class Transactions extends Entity implements \JsonSerializable
{
    /**
     * @var \PhilKra\Stores\TransactionsStore
     */
    private $store;

    /**
     * @param ErrorsStore $store
     */
    public function __construct(Config $config, TransactionsStore $store)
    {
        parent::__construct($config);
        $this->store = $store;
    }

    /**
     * Serialize Transactions Data to JSON "ready" Array
     *
     * @return array
     * @throws UnsupportedApmVersionException
     */
    public function jsonSerialize()
    {
        if ($this->config->useVersion1()) {
            return $this->getSkeleton() + [
                    'transactions' => $this->store
                ];
        }

        if ($this->config->useVersion2()) {
            return $this->makeVersion2Json();
        }

        throw new UnsupportedApmVersionException($this->config->apmVersion());
    }

    private function makeVersion2Json(): array
    {
        if ($this->store->isEmpty()) {
            return $this->getSkeleton();
        }

        $transactionData = json_decode(json_encode($this->store), true);

        $encodedTransactions = [];

        foreach ($transactionData as $transaction) {
            $encodedTransactions[] = array_merge($this->getSkeleton(), $transaction);
        }

        return $encodedTransactions;
    }
}
