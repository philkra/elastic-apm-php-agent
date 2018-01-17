<?php
namespace PhilKra\Events;

use \PhilKra\Helper\Timer;

/**
 *
 * Abstract Transaction class for all inheriting Transactions
 *
 */
class Transaction extends EventBean {

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
  ];

  /**
   * Is the Transaction running ?
   *
   * @var bool
   */
  private $running = false;

  /**
   * Create the Transaction
   *
   * @param final string $name
   */
  public function __construct( string $name ) {
    parent::__construct();
    $this->setTransactionName( $name );
    $this->timer = new Timer();
  }

  /**
   * Start the Transaction
   *
   * @return void
   */
  public function start() {
    $this->timer->start();
    $this->running = true;
  }

  /**
   * Stop the Transaction
   *
   * @return void
   */
  public function stop() {
    // Stop the Timer & Set the Status to not running
    $this->timer->stop();
    $this->running = false;

    // Store Summary
    $this->summary['duration']  = $this->timer->getDuration();
    $this->summary['backtrace'] = debug_backtrace();
  }

  /**
   * Set the Transaction Name
   *
   * @param string $name
   *
   * @return void
   */
  public function setTransactionName( string $name ) {
    $this->name = $name;
  }

  /**
   * Get the Transaction Name
   *
   * @return string
   */
  public function getTransactionName() : string {
    return $this->name;
  }

  /**
   * Get the Summary of this Transaction
   *
   * @return array
   */
  public function getSummary() : array {
    return $this->summary;
  }

}
