<?php
namespace PhilKra\Tests\Transaction;

use \PhilKra\Transaction\Summary;
use \PhilKra\Instrumentation\Timer;
use \PHPUnit\Framework\TestCase;

/**
 * Test Case for @see \PhilKra\Transaction\Summary
 */
final class SummaryTest extends TestCase {

  /**
   * @covers \PhilKra\Transaction\Summary::__construct
   * @covers \PhilKra\Transaction\Summary::getDuration
   * @covers \PhilKra\Transaction\Summary::getBacktrace
   */
  public function testTransactionRegistrationAndFetch() {
    // Tear Up a Timer with Duration
    $timer = new Timer();
    $timer->start();
    usleep( 25 );
    $timer->stop();

    // Store the Backtrace for Comparing
    $backtrace = debug_backtrace();

    // Setup the Summary Object
    $summary = new Summary( $timer->getDuration(), $backtrace );

    // Control Accessors
    $this->assertEquals( $timer->getDuration(), $summary->getDuration() );
    $this->assertEquals( $backtrace, $summary->getBacktrace() );
  }

}
