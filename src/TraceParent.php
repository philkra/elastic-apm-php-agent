<?php

namespace PhilKra;

use PhilKra\Exception\InvalidTraceContextHeaderException;

class TraceParent
{
    const HEADER_NAME = 'elastic-apm-traceparent';

    /**
     * @link https://www.w3.org/TR/trace-context/#version
     */
    const VERSION = '00';

    /**
     * @var string
     */
    private $traceId;

    /**
     * @var string
     */
    private $parentId;

    /**
     * @var string
     */
    private $traceFlags;

    /**
     * @param string $traceId
     * @param string $parentId
     * @param string $traceFlags
     */
    public function __construct(string $traceId, string $parentId, string $traceFlags)
    {
        $this->traceId = $traceId;
        $this->parentId = $parentId;
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
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param string $parentId
     */
    public function setParentId(string $parentId)
    {
        $this->parentId = $parentId;
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
     * @link https://www.w3.org/TR/trace-context/#version-format
     * @return bool
     */
    public static function isValidHeader(string $header)
    {
        return preg_match('/^'.self::VERSION.'-[\da-f]{32}-[\da-f]{16}-[\da-f]{2}$/', $header) === 1;
    }

    /**
     * @param string $header
     * @return TraceParent
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
            self::VERSION,
            $this->getTraceId(),
            $this->getParentId(),
            $this->getTraceFlags()
        );
    }
}