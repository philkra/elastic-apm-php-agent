<?php

namespace PhilKra\Events;

interface EventFactoryInterface
{
    /**
     * Creates a new error.
     * 
     * @param \Throwable $throwable
     * @param array      $contexts
     *
     * @return Error
     */
    public function createError(\Throwable $throwable, array $contexts): Error;

    /**
     * Creates a new transaction
     *
     * @param string $name
     * @param array  $contexts
     */
    public function createTransaction(string $name, array $contexts, float $start = null): Transaction;
}
