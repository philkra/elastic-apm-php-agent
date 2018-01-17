<?php
namespace PhilKra\Error;

/**
 * Bean for Error/Exception wrapping
 */
class Bean {

  /**
   * Error occured after x microseconds
   *
   * @var float
   */
  private $occurredAfter;

  /**
   * Error occurred on Timestamp
   *
   * @var int
   */
  private $occurredOn;

  /**
   * Error/Exception
   *
   * @var Throwable
   */
  private $error;

  /**
   * @param float     $occurredAfter
   * @param Throwable $error
   */
  public function __construct( float $occurredAfter, Throwable $error ) {
    $this->occurredAfter = $occurredAfter;
    $this->occurredOn    = time();
    $this->error         = $error;
  }

}
