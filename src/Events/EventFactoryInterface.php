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
     * @param string     $name
     * @param array      $contexts
     * @param float|null $start
     * @return Transaction
     */
    public function createTransaction(string $name, array $contexts, ?float $start = null): Transaction;

    /**
     * Creates a span inside an existing transaction
     *
     * @param string      $name
     * @param array       $contexts
     * @param Transaction $parentTransaction
     * @param Span|null   $parentSpan
     * @return Span
     */
    public function createSpan(string $name, array $contexts, Transaction $parentTransaction, ?Span $parentSpan = null): Span;
}
