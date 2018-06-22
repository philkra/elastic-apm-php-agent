<?php

namespace PhilKra\Exception\Transaction;

/**
 * Trying to fetch an unregistered Transaction
 */
class UnknownTransactionException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The transaction "%s" is not registered.', $message), $code, $previous);
    }
}
