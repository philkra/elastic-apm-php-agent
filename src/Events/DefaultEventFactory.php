<?php

namespace PhilKra\Events;

final class DefaultEventFactory implements EventFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createError(\Throwable $throwable, array $contexts): Error
    {
        return new Error($throwable, $contexts);
    }

    /**
     * {@inheritdoc}
     */
    public function createTransaction(string $name, array $contexts, float $start = null): Transaction
    {
        return new Transaction($name, $contexts, $start);
    }
}
