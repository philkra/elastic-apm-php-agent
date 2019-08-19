<?php

namespace PhilKra;

use PhilKra\Exception\InvalidTraceContextHeaderException;

class TraceParent
{
    const HEADER_NAME = 'elastic-apm-traceparent';
    /**
     * @var string
     */
    private $traceId;
    /**
     * @var string
     */
    private $spanId;
    /**
     * @var string
     */
    private $traceFlags;
    /**
     * @param string $traceId
     * @param string $spanId
     * @param string $traceFlags
     */
    public function __construct(string $traceId, string $spanId, string $traceFlags)
    {
        $this->traceId = $traceId;
        $this->spanId = $spanId;
        $this->traceFlags = $traceFlags;
    }
    /**
     * @return string
     */
    public function getTraceId()
    {
        return $this->traceId;
    }
    /**
     * @param string $traceId
     */
    public function setTraceId(string $traceId)
    {
        $this->traceId = $traceId;
    }
    /**
     * @return string
     */
    public function getSpanId()
    {
        return $this->spanId;
    }
    /**
     * @param string $spanId
     */
    public function setSpanId(string $spanId)
    {
        $this->spanId = $spanId;
    }
    /**
     * @return string
     */
    public function getTraceFlags()
    {
        return $this->traceFlags;
    }
    /**
     * @param string $traceFlags
     */
    public function setTraceFlags(string $traceFlags)
    {
        $this->traceFlags = $traceFlags;
    }

    /**
     * @param string $header
     * @return bool
     */
    public static function isValidHeader(string $header)
    {
        return preg_match('/^00-[\da-f]{32}-[\da-f]{16}-[\da-f]{2}$/', $header) === 1;
    }

    /**
     * @param string $header
     * @return php
     * @throws InvalidTraceContextHeaderException
     */
    public static function createFromHeader(string $header)
    {
        if (!self::isValidHeader($header)) {
            throw new InvalidTraceContextHeaderException("InvalidTraceContextHeaderException");
        }
        $parsed = explode('-', $header);
        return new TraceParent($parsed[1], $parsed[2], $parsed[3]);
    }

    public function __toString()
    {
        return sprintf(
            '%s-%s-%s-%s',
            '00',
            $this->getTraceId(),
            $this->getSpanId(),
            $this->getTraceFlags()
        );
    }
}