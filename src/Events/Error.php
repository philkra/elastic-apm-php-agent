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
    public function __construct(\Throwable $throwable, array $contexts)
    {
        parent::__construct($contexts);
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
            'id'        => $this->getId(),
            'timestamp' => $this->getTimestamp(),
            'context'   => $this->getContext(),
            'culprit'   => sprintf('%s:%d', $this->throwable->getFile(), $this->throwable->getLine()),
            'exception' => [
                'message'    => $this->throwable->getMessage(),
                'type'       => get_class($this->throwable),
                'code'       => $this->throwable->getCode(),
                'stacktrace' => $this->mapStacktrace(),
            ],
            'processor' => [
                'event' => 'error',
                'name'  => 'error',
            ]
        ];
    }

    /**
     * Map the Stacktrace to Schema
     *
     * @return array
     */
    private function mapStacktrace() : array
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
