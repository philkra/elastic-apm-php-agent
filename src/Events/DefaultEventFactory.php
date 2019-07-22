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
}
