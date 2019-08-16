<?php

namespace PhilKra\Events;

/**
 *
 * Event Bean for Error wrapping
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.2/errors.html
 *
 */
class Error extends EventBean implements \JsonSerializable
{
    /**
     * Error | Exception
     *
     * @link http://php.net/manual/en/class.throwable.php
     *
     * @var Throwable
     */
    private $throwable;

    /**
     * @param Throwable $throwable
     * @param array $contexts
     */
    public function __construct(\Throwable $throwable, array $contexts, ?Transaction $transaction = null)
    {
        parent::__construct($contexts, $transaction);
        $this->throwable = $throwable;
    }

    /**
     * Serialize Error Event
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'error' => [
                'id'             => $this->getId(),
                'transaction_id' => $this->getParentId(),
                'parent_id'      => $this->getParentId(),
                'trace_id'       => $this->getTraceId(),
                'timestamp'      => $this->getTimestamp(),
                'context'        => $this->getContext(),
                'culprit'        => sprintf('%s:%d', $this->throwable->getFile(), $this->throwable->getLine()),
                'exception'      => [
                    'message'    => $this->throwable->getMessage(),
                    'type'       => get_class($this->throwable),
                    'code'       => $this->throwable->getCode(),
                    'stacktrace' => $this->mapStacktrace($this->throwable->getTrace()),
                ],
            ]
        ];
    }
}
