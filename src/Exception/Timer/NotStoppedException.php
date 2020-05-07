<?php

namespace PhilKra\Exception\Timer;

use Exception;

/**
 * Trying to get the Duration of a running Timer
 */
class NotStoppedException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Can\'t get the duration of a running timer.', $code, $previous);
    }
}
