<?php
namespace PhilKra\Tests;

use \PhilKra\Agent;
use \PhilKra\Transaction\Summary;
use \PHPUnit\Framework\TestCase;

/**
 * Test Case for @see \PhilKra\Agent
 */
final class AgentTest extends TestCase {

  /**
   * @covers \PhilKra\Agent::__construct
   * @covers \PhilKra\Agent::startTransaction
   * @covers \PhilKra\Agent::stopTransaction
   */
  public function testStartAndStopATransaction() {
    $agent = new Agent( [ 'appName' => 'phpunit_1' ] );

    // Create a Transaction, wait and Stop it
    $name = 'trx';
    $agent->startTransaction( $name );
    usleep( 10 );
    $agent->stopTransaction( $name );

    // Transaction Summary must be populated
    $summary = $agent->getTransactionSummary( $name );
    $this->assertNotNull( $summary );
    $this->assertGreaterThanOrEqual( 10, $summary->getDuration() );
    $this->assertNotEmpty( $summary->getBacktrace() );
  }

  /**
   * @depends testStartAndStopATransaction
   *
   * @expectedException \PhilKra\Exception\Transaction\UnknownTransactionException
   *
   * @covers \PhilKra\Agent::__construct
   * @covers \PhilKra\Agent::stopTransaction
   */
  public function testForceErrorOnUnstartedTransaction() {
    $agent = new Agent( [ 'appName' => 'phpunit_2' ] );

    // Stop an unstarted Transaction and let it go boom!
    $agent->stopTransaction( 'unknown' );
  }

  /**
   * @depends testForceErrorOnUnstartedTransaction
   *
   * @covers \PhilKra\Agent::__construct
   * @covers \PhilKra\Agent::getTransactionSummary
   */
  public function testForceErrorOnSummaryOfUnstartedTransaction() {
    $agent = new Agent( [ 'appName' => 'phpunit_3' ] );

    $summary = $agent->getTransactionSummary( 'unknown' );
    $this->assertNull( $summary );
  }

}
