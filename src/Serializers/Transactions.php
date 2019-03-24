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
        if ($this->useVersion1()) {
            return $this->getSkeleton() + [
                    'transactions' => $this->store
                ];
        }

        // TODO v2 should return a collection of transactions
        // The agent send() method must send each v2 transaction as separate requests
        if ($this->useVersion2()) {
            return $this->makeVersion2Json();
        }

        throw new UnsupportedApmVersionException($this->apmVersion());
    }

    private function makeVersion2Json(): array
    {
        if ($this->store->isEmpty()) {
            return $this->getSkeleton();
        }

        $transactionData = json_decode(json_encode($this->store), true);

        return array_merge($this->getSkeleton(), $transactionData[0]);
    }

    private function apmVersion(): string
    {
        return $this->config->get('apmVersion');
    }

    private function useVersion1(): bool
    {
        return $this->config->get('apmVersion') === 'v1';
    }

    private function useVersion2(): bool
    {
        return $this->config->get('apmVersion') === 'v2';
    }
}
