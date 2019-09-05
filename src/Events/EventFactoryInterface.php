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
    public function newError(\Throwable $throwable, array $contexts, ?Transaction $parent = null): Error;

    /**
     * Creates a new transaction
     *
     * @param string $name
     * @param array  $contexts
     *
     * @return Transaction
     */
    public function newTransaction(string $name, array $contexts, float $start = null): Transaction;

    /**
     * Creates a new Span
     *
     * @param string    $name
     * @param EventBean $parent
     *
     * @return Span
     */
    public function newSpan(string $name, EventBean $parent): Span;

    /**
     * Creates a new Metricset
     *
     * @link https://www.elastic.co/guide/en/apm/server/7.3/metricset-api.html
     * @link https://github.com/elastic/apm-server/blob/master/docs/spec/metricsets/metricset.json
     *
     * @param array $set, k-v pair ['sys.avg.load' => 89]
     * @param array $tags, Default []
     *
     * @return Metricset
     */
    public function newMetricset($set, $tags): Metricset;

}
