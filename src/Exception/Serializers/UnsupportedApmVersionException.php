<?php

namespace PhilKra\Exception\Serializers;

/**
 * Trying to use an unsupported APM Version
 */
class UnsupportedApmVersionException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The APM version "%s" is not supported.', $message), $code, $previous);
    }
}
