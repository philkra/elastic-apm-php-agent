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
   * @var string
   */
  private $timestamp;

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
    $this->timestamp     = date( 'YYYY-MM-DDTHH:mm:ss.sssZ' );
    $this->error         = $error;
  }

}
