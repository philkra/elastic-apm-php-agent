<?php

namespace PhilKra\Events;

use PhilKra\Exception\InvalidTraceContextHeaderException;
use PhilKra\Helper\Timer;
use PhilKra\TraceParent;

/**
 *
 * Abstract Transaction class for all inheriting Transactions
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/transaction-api.html
 *
 */
class Transaction extends EventBean implements \JsonSerializable
{
    /**
     * Transaction Name
     *
     * @var string
     */
    private $name;

    /**
     * Transaction Timer
     *
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
     * The spans for the transaction
     *
     * @var array
     */
    private $spans = [];

    /**
     * The errors for the transaction
     *
     * @var array
     */
    private $errors = [];

    /**
     * Backtrace Depth
     *
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
        $this->setTraceContext();
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
     * Set the spans for the transaction
     *
     * @param array $spans
     *
     * @return void
     */
    public function setSpans(array $spans)
    {
        $this->spans = $spans;
    }

    public function addError(Error $error)
    {
        $this->errors[] = $error;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors ?? [];
    }

    /**
     * Set the Max Depth/Limit of the debug_backtrace method
     *
     * @link http://php.net/manual/en/function.debug-backtrace.php
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/55
     *
     * @param int $limit [description]
     */
    public function setBacktraceLimit(int $limit)
    {
        $this->backtraceLimit = $limit;
    }

    /**
     * Get the spans from the transaction
     *
     * @return array
     */
    private function getSpans(): array
    {
        return $this->spans;
    }

    /**
     * Set Trace context
     *
     * @throws \Exception
     */
    private function setTraceContext()
    {
        $headers = getallheaders();
        $traceParentHeader = $headers[TraceParent::HEADER_NAME] ?? null;
        if ($traceParentHeader !== null) {
            try {
                $traceParent = TraceParent::createFromHeader($traceParentHeader);
                $this->setTraceId($traceParent->getTraceId());
                $this->setParentId($traceParent->getSpanId());
            } catch (InvalidTraceContextHeaderException $e) {
                $this->setTraceId(self::generateRandomBitsInHex(self::TRACE_ID_SIZE));
            }
        } else {
            $this->setTraceId(self::generateRandomBitsInHex(self::TRACE_ID_SIZE));
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
          'id'        => $this->getId(),
          'trace_id'  => $this->getTraceId(),
          'parent_id' => $this->getParentId(),
          'span_count' => [
              'started' => count($this->getSpans()),
              'dropped' => 0
          ],
          'timestamp' => $this->getTimestamp(),
          'name'      => $this->getTransactionName(),
          'duration'  => $this->summary['duration'],
          'type'      => $this->getMetaType(),
          'result'    => $this->getMetaResult(),
          'context'   => $this->getContext(),
          'errors'     => $this->getErrors(),
          'spans'     => $this->getSpans(),
          'errors'    => $this->getErrors(),
          'processor' => [
              'event' => 'transaction',
              'name'  => 'transaction',
          ]
      ];
    }
}
