<?php

namespace PhilKra\EventsV2;

use PhilKra\Events\EventFactoryInterface;

final class DefaultEventFactory implements EventFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createError(\Throwable $throwable, array $contexts): \PhilKra\Events\Error
    {
        return new Error($throwable, $contexts);
    }

    /**
     * {@inheritdoc}
     */
    public function createTransaction(string $name, array $contexts, float $start = null): \PhilKra\Events\Transaction
    {
        return new Transaction($name, $contexts, $start);
    }
}
