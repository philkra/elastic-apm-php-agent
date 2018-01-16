<?php
namespace PhilKra\Transaction;

/**
 * Bean containing the Transaction Summary
 */
class Summary {

  /**
   * Transaction Duration
   *
   * @var float
   */
  private $duration;

  /**
   * Backtrace of the Transaction
   *
   * @link http://php.net/manual/en/function.debug-backtrace.php
   *
   * @var array
   */
  private $backtrace;

  /**
   * Create the Summary Bean
   *
   * @param float $duration
   * @param array $backtrace
   */
  public function __construct( float $duration, array $backtrace ) {
    $this->duration  = $duration;
    $this->backtrace = $backtrace;
  }

  /**
   * Get the Transaction duration
   *
   * @return float
   */
  public function getDuration() : float {
    return $this->duration;
  }

  /**
   * Get the Transaction's Backtrace
   *
   * @return array
   */
  public function getBacktrace() : array {
    return $this->backtrace;
  }

}
