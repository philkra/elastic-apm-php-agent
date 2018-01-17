<?php
namespace PhilKra\Helper;

use \PhilKra\Exception\Timer\NotStartedException;
use \PhilKra\Exception\Timer\NotStoppedException;

/**
 * Timer for Duration tracing
 */
class Timer {

  /**
   * Starting Timestamp
   *
   * @var double
   */
  private $startedOn = null;

  /**
   * Ending Timestamp
   *
   * @var double
   */
  private $stoppedOn = null;

  /**
   * Start the Timer
   *
   * @return void
   */
  public function start() {
    $this->startedOn = microtime( true );
  }

  /**
   * Stop the Timer
   *
   * @throws \PhilKra\Exception\Timer\NotStartedException
   *
   * @return void
   */
  public function stop() {
    if( $this->startedOn === null ) {
      throw new NotStartedException();
    }

    $this->stoppedOn = microtime( true );
  }

  /**
   * Get the elapsed Duration of this Timer
   *
   * @throws \PhilKra\Exception\Timer\NotStoppedException
   *
   * @return float
   */
  public function getDuration() : float {
    if( $this->stoppedOn === null ) {
      throw new NotStoppedException();
    }

    return $this->stoppedOn - $this->startedOn;
  }

}
