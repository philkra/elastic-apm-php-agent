<?php

namespace PhilKra\Events;

final class DefaultEventFactory implements EventFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function newError(\Throwable $throwable, array $contexts, ?Transaction $parent = null): Error
    {
        return new Error($throwable, $contexts, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function newTransaction(string $name, array $contexts, float $start = null): Transaction
    {
        return new Transaction($name, $contexts, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function newSpan(string $name, EventBean $parent): Span
    {
        return new Span($name, $parent, $contexts, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function newMetricset(array $set, array $tags = []): Metricset
    {
        return new Metricset($set, $tags);
    }

}
