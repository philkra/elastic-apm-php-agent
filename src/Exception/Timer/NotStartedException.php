<?php

namespace PhilKra\Exception\Timer;

use Exception;

/**
 * Trying to stop a Timer that has not been started
 */
class NotStartedException extends Exception
{

    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Can\'t stop a timer which isn\'t started.', $code, $previous);
    }

}
