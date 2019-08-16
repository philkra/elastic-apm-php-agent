<?php

namespace PhilKra\Exception\Transaction;

/**
 * Trying to create a span when a transaction is no in progress
 */
class NoTransactionInProgressException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The span "%s" requires a transaction.', $message), $code, $previous);
    }
}
