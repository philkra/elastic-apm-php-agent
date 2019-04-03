<?php

namespace PhilKra\SerializersV2;

use PhilKra\Helper\Config;

/**
 *
 * Convert the Spans to V2 JSON Schema
 *
 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
 *
 */
class Spans implements \JsonSerializable
{
    /**
     * @var array
     */
    private $spans;

    /**
     * @param ErrorsStore $store
     */
    public function __construct(Config $config, array $spans)
    {
        $this->config = $config;
        $this->spans = $spans;
    }

    /**
     * Serialize Error Data to JSON "ready" Array
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $spans = [];
        foreach ($this->spans as $span) {
            $spans[] = ['span' => $span];
        }

        return $spans;

    }
}
