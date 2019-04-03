<?php

namespace PhilKra\EventsV2;

/**
 *
 * Event Bean for Error wrapping
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.2/errors.html
 *
 */
class Error extends \PhilKra\Events\Error
{
    /**
     * Serialize Error Event
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'error'=> [
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
            ]
        ];
    }

}
