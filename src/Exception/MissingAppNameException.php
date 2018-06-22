<?php

namespace PhilKra\Exception;

/**
 * Application Tear Up has missing App Name in Config
 */
class MisingAppNameException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('No app name registered in agent config.', $message), $code, $previous);
    }
}
