<?php

namespace PhilKra\Events;

final class DefaultEventFactory implements EventFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createError(\Throwable $throwable, array $contexts, ?Transaction $transaction = null): Error
    {
        return new Error($throwable, $contexts, $transaction);
    }

    /**
     * {@inheritdoc}
     */
    public function createTransaction(string $name, array $contexts, float $start = null): Transaction
    {
        return new Transaction($name, $contexts, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function createSpan(string $name, array $contexts, Transaction $parentTransaction, ?Span $parentSpan = null): Span
    {
        return new Span($name, $contexts, $parentTransaction, $parentSpan);
    }
}
