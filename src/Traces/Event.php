<?php

namespace PhilKra\Traces;

use Ramsey\Uuid\Uuid;

/**
 *
 * Event Trace with Timing Context
 *
 * e.g. Error, Transaction, Span
 *
 */
class Event extends TimedTrace
{

    /**
     * Hex encoded 64 random bits ID of the span
     *
     * @var string
     */
    private $id;

    /**
     * Hex encoded 128 random bits ID of the correlated trace.
     *
     * @var string
     */
    private $traceId;

    /**
     * Hex encoded 64 random bits ID of the parent transaction or span.
     *
     * @var string
     */
    private $parentId;

    /**
     * Init the Event with the Timestamp and UUID
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/3
     *
     * @param array $contexts
     */
    public function __construct()
    {
        parent::__construct();

        $this->id = $this->generateId();
    }

    /**
     * Get the Event Id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get the Trace Id
     *
     * @return string
     */
    public function getTraceId() : string
    {
        return $this->traceId;
    }

    /**
     * Set the Span's Trace Id
     *
     * @param string $id
     *
     * @return void
     */
    public function setTraceId(string $id) : void
    {
        $this->traceId = $id;
    }

    /**
     * Get the Events Parent Id
     *
     * @return string|null
     */
    public function getParentId() : ?string
    {
        return $this->parentId;
    }

    /**
     * Set the Span's Parent Id
     *
     * @param string $id
     *
     * @return void
     */
    public function setParentId(?string $id) : void
    {
        $this->parentId = $id;
    }

    /**
     * Generate a hexdecimal Id
     *
     * <i>64 bit hex</id>
     *
     * @return string
     */
    public function generateId() : string
    {
        return sprintf("%x", mt_rand(1000, 9999));
    }

    /**
     * Generate and write a hexdecimal Trace Id
     *
     * <i>128 bit hex</id>
     *
     * @return string
     */
    public function generateTraceId() : string
    {
        return sprintf("%x", mt_rand(100000000, 999999999));
    }

}
