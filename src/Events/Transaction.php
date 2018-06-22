<?php

namespace PhilKra\Events;

use PhilKra\Helper\Timer;

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
     * The spams for the transaction
     *
     * @var array
     */
    private $spans = [];

    /**
    * Create the Transaction
    *
    * @param string $name
    * @param array $contexts
    */
    public function __construct(string $name, array $contexts)
    {
        parent::__construct($contexts);
        $this->setTransactionName($name);
        $this->timer = new Timer();
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
        $this->summary['duration']  = $duration ?? round($this->timer->getDuration(), 3);
        $this->summary['headers']   = (function_exists('xdebug_get_headers') === true) ? xdebug_get_headers() : [];
        $this->summary['backtrace'] = debug_backtrace();
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
    * Serialize Transaction Event
    *
    * @return array
    */
    public function jsonSerialize() : array
    {
        return [
          'id'        => $this->getId(),
          'timestamp' => $this->getTimestamp(),
          'name'      => $this->getTransactionName(),
          'duration'  => $this->summary['duration'],
          'type'      => $this->getMetaType(),
          'result'    => $this->getMetaResult(),
          'context'   => $this->getContext(),
          'spans'     => $this->getSpans(),
      ];
    }

    private function getSpans(): array
    {
        return $this->spans;
    }
}
