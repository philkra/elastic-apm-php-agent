<?php
namespace PhilKra\Transaction;

use \PhilKra\Instrumentation\Timer;
use \PhilKra\Transaction\Summary;

/**
 * Abstract Transaction class for all inheriting Transactions
 */
class Transaction implements ITransaction {

  /**
   * Transaction Name
   *
   * @var string
   */
  private $name;

  /**
   * Transaction Timer
   *
   * @var \PhilKra\Instrumentation\Timer
   */
  private $timer;

  /**
   * Summary of this Transaction
   *
   * @var \PhilKra\Transaction\Summary
   */
  private $summary;

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
    $this->setTransactionName( $name );
    $this->timer = new Timer();
  }

  /**
   * @see \PhilKra\Transaction|ITransaction::start
   */
  public function start() {
    $this->timer->start();
    $this->running = true;
  }

  /**
   * @see \PhilKra\Transaction|ITransaction::stop
   */
  public function stop() {
    // Stop the Timer & Set the Status to not running
    $this->timer->stop();
    $this->running = false;

    // Store Summary
    $this->summary = new Summary(
      $this->timer->getDuration(),
      debug_backtrace()
    );
  }

  /**
   * @see \PhilKra\Transaction|ITransaction::setTransactionName
   */
  public function setTransactionName( string $name ) {
    $this->name = $name;
  }

  /**
   * @see \PhilKra\Transaction|ITransaction::getTransactionName
   */
  public function getTransactionName() : string {
    return $this->name;
  }

  /**
   * Get the Summary of this Transaction
   *
   * <i>
   *  Watch out, as the case that Summary isn't created yet is not covered.
   * </i>
   *
   * @return \PhilKra\Transaction\Summary
   */
  public function getSummary() : Summary {
    return $this->summary;
  }

  /**
   * Commit the Transaction trace to the APM Server
   *
   * @return bool
   */
  public function send() : bool {

  }

}
