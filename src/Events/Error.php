<?php

namespace PhilKra\Events;

use PhilKra\Helper\Encoding;
use PhilKra\Traits\Events\Stacktrace;

/**
 *
 * Event Bean for Error wrapping
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.2/errors.html
 *
 */
class Error extends EventBean implements \JsonSerializable
{
    use Stacktrace;

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
                'culprit'        => Encoding::keywordField(sprintf('%s:%d', $this->throwable->getFile(), $this->throwable->getLine())),
                'exception'      => [
                    'message'    => $this->throwable->getMessage(),
                    'type'       => Encoding::keywordField(get_class($this->throwable)),
                    'code'       => $this->throwable->getCode(),
                    'stacktrace' => $this->mapStacktrace(),
                ],
            ]
        ];
    }

    /**
     * Map the Stacktrace to Schema
     *
     * @return array
     */
    final private function mapStacktrace() : array
    {
        $stacktrace = [];

        foreach ($this->throwable->getTrace() as $trace) {
            $item = [
              'function' => $trace['function'] ?? '(closure)'
            ];

            if (isset($trace['line']) === true) {
                $item['lineno'] = $trace['line'];
            }

            if (isset($trace['file']) === true) {
                $item += [
                    'filename' => basename($trace['file']),
                    'abs_path' => $trace['file']
                ];
            }

            if (isset($trace['class']) === true) {
                $item['module'] = $trace['class'];
            }
            if (isset($trace['type']) === true) {
                $item['type'] = $trace['type'];
            }

            if (!isset($item['lineno'])) {
                $item['lineno'] = 0;
            }

            if (!isset($item['filename'])) {
                $item['filename'] = '(anonymous)';
            }

            array_push($stacktrace, $item);
        }

        return $stacktrace;
    }

}
