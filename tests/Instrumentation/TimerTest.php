<?php
namespace PhilKra\Tests\Instrumentation;

use \PhilKra\Instrumentation\Timer;
use \PHPUnit\Framework\TestCase;

/**
 * Test Case for @see \PhilKra\Instrumentation\Timer
 */
final class TimerTest extends TestCase {

  /**
   * @covers \PhilKra\Instrumentation\Timer::start
   * @covers \PhilKra\Instrumentation\Timer::stop
   * @covers \PhilKra\Instrumentation\Timer::getDuration
   */
  public function testCanBeStartedAndStoppedWithDuration() {
    $timer = new Timer();
    $duration = rand( 25, 100 );

    $timer->start();
    usleep( $duration );
    $timer->stop();

    $this->assertGreaterThanOrEqual( $duration / 1000000, $timer->getDuration() );
  }

  /**
   * @depends testCanBeStartedAndStoppedWithDuration
   *
   * @expectedException \PhilKra\Exception\Timer\NotStoppedException
   *
   * @covers \PhilKra\Instrumentation\Timer::start
   * @covers \PhilKra\Instrumentation\Timer::getDuration
   */
  public function testCanBeStartedWithForcingDurationException() {
    $timer = new Timer();
    $timer->start();
    $timer->getDuration();
  }

  /**
   * @depends testCanBeStartedWithForcingDurationException
   *
   * @expectedException \PhilKra\Exception\Timer\NotStartedException
   *
   * @covers \PhilKra\Instrumentation\Timer::stop
   */
  public function testCannotBeStoppedWithoutStart() {
    $timer = new Timer();
    $timer->stop();
  }

}
