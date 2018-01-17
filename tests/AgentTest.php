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
   * @covers \PhilKra\Agent::getConfig
   * @covers \PhilKra\Agent::getDefaultConfig
   */
  public function testControlDefaultConfig() {
    $appName = sprintf( 'app_name_%d', rand( 10, 99 ) );
    $agent = new Agent( [ 'appName' => $appName ] );

    // Control Default Config
    $config = $agent->getConfig();

    $this->assertArrayHasKey( 'appName', $config );
    $this->assertArrayHasKey( 'secretToken', $config );
    $this->assertArrayHasKey( 'serverUrl', $config );
    $this->assertArrayHasKey( 'hostname', $config );
    $this->assertArrayHasKey( 'timeout', $config );

    $this->assertEquals( $config['appName'], $appName );
    $this->assertNull( $config['secretToken'] );
    $this->assertEquals( $config['serverUrl'], 'http://127.0.0.1:8200' );
    $this->assertEquals( $config['hostname'], gethostname() );
    $this->assertEquals( $config['timeout'], 5 );
  }

  /**
   * @depends testControlDefaultConfig
   *
   * @covers \PhilKra\Agent::__construct
   * @covers \PhilKra\Agent::getConfig
   * @covers \PhilKra\Agent::getDefaultConfig
   */
  public function testControlInjectedConfig() {
    $init = [
      'appName'       => sprintf( 'app_name_%d', rand( 10, 99 ) ),
      'secretToken'   => hash( 'tiger128,3', time() ),
      'serverUrl'     => sprintf( 'https://node%d.domain.tld:%d', rand( 10, 99 ), rand( 1000, 9999 ) ),
      'appVersion'    => sprintf( '%d.%d.42', rand( 0, 3 ), rand( 0, 10 ) ),
      'frameworkName' => uniqid(),
      'timeout'       => rand( 10, 20 ),
      'hostname'      => sprintf( 'host_%d', rand( 0, 9 ) ),
    ];

    $agent = new Agent( $init );

    // Control Default Config
    $config = $agent->getConfig();
    foreach( $init as $key => $value ) {
        $this->assertEquals( $config[$key], $init[$key], 'key: ' . $key );
    }
  }

  /**
   * @depends testControlInjectedConfig
   *
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
