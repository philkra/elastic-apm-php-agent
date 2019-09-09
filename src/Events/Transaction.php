<?php

namespace PhilKra\Events;

use PhilKra\Helper\Timer;
use PhilKra\Helper\DistributedTracing;

/**
 *
 * Transaction Event
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/transaction-api.html
 *
 */
class Transaction extends TraceableEvent implements \JsonSerializable
{
    /**
     * Transaction Name
     *
     * @var string
     */
    private $name;

    /**
     * @var \PhilKra\Helper\Timer
     */
    private $timer;

    /**
     * Summary of this Transaction
     *
     * @var array
     */
    private $summary = [
        'duration'  => 0.0,
        'backtrace' => null,
        'headers'   => []
    ];

    /**
     * @var int
     */
    private $backtraceLimit = 0;

    /**
    * Create the Transaction
    *
    * @param string $name
    * @param array $contexts
    */
    public function __construct(string $name, array $contexts, $start = null)
    {
        parent::__construct($contexts);
        $this->setTransactionName($name);
        $this->timer = new Timer($start);
    }

    /**
    * Start the Transaction
    *
    * @return void
    */
    public function start()
    {
        $this->timer->start();
    }

    /**
     * Stop the Transaction
     *
     * @param integer|null $duration
     *
     * @return void
     */
    public function stop(int $duration = null)
    {
        // Stop the Timer
        $this->timer->stop();

        // Store Summary
        $this->summary['duration']  = $duration ?? round($this->timer->getDurationInMilliseconds(), 3);
        $this->summary['headers']   = (function_exists('xdebug_get_headers') === true) ? xdebug_get_headers() : [];
        $this->summary['backtrace'] = debug_backtrace($this->backtraceLimit);
    }

    /**
    * Set the Transaction Name
    *
    * @param string $name
    *
    * @return void
    */
    public function setTransactionName(string $name)
    {
        $this->name = $name;
    }

    /**
    * Get the Transaction Name
    *
    * @return string
    */
    public function getTransactionName() : string
    {
        return $this->name;
    }

    /**
    * Get the Summary of this Transaction
    *
    * @return array
    */
    public function getSummary() : array
    {
        return $this->summary;
    }

    /**
     * Set the Max Depth/Limit of the debug_backtrace method
     *
     * @link http://php.net/manual/en/function.debug-backtrace.php
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/55
     *
     * @param int $limit
     */
    public function setBacktraceLimit(int $limit)
    {
        $this->backtraceLimit = $limit;
    }

    public function getDistributedTracing() : string
    {
        return (new DistributedTracing($this->getTraceId(), $this->getParentId()))->__toString();
    }

    /**
     * Set Trace context
     *
     * @throws \Exception
     */
    private function setTraceContext()
    {
        // Is one of the Traceparent Headers populated ?
        $header = $_SERVER['HTTP_' . DistributedTracing::HEADER_NAME] ?? ($_SERVER['HTTP_TRACEPARENT'] ?? null);
        if ($header !== null && DistributedTracing::isValidHeader($header) === true) {
            try {
                $traceParent = DistributedTracing::createFromHeader($header);

                $this->setTraceId($traceParent->getTraceId());
                $this->setParentId($traceParent->getParentId());
            }
            catch (InvalidTraceContextHeaderException $e) {
                $this->setTraceId(self::generateRandomBitsInHex(self::TRACE_ID_BITS));
            }
        }
        else {
            $this->setTraceId(self::generateRandomBitsInHex(self::TRACE_ID_BITS));
        }
    }

    /**
     * Serialize Transaction Event
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'transaction' => [
                'trace_id'   => $this->getTraceId(),
                'id'         => $this->getId(),
                'parent_id'  => $this->getParentId(),
                'type'       => $this->getMetaType(),
                'duration'   => $this->summary['duration'],
                'timestamp'  => $this->getTimestamp(),
                'result'     => $this->getMetaResult(),
                'name'       => $this->getTransactionName(),
                'context'    => $this->getContext(),
                'sampled'    => null,
                'span_count' => [
                    'started' => 0,
                    'dropped' => 0,
                ],
            ]
        ];
    }
}
