<?php

namespace PhilKra\Exception\Transaction;

/**
 * Trying to register a already registered Transaction
 */
class DuplicateTransactionNameException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('A transaction with the name %s is already registered.', $message), $code, $previous);
    }
}
