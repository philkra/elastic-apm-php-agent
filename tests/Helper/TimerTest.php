<?php
namespace PhilKra\Tests\Helper;

use \PhilKra\Helper\Timer;
use \PHPUnit\Framework\TestCase;

/**
 * Test Case for @see \PhilKra\Helper\Timer
 */
final class TimerTest extends TestCase {

  /**
   * @covers \PhilKra\Helper\Timer::start
   * @covers \PhilKra\Helper\Timer::stop
   * @covers \PhilKra\Helper\Timer::getDuration
   * @covers \PhilKra\Helper\Timer::toMicro
   */
  public function testCanBeStartedAndStoppedWithDuration() {
    $timer = new Timer();
    $duration = rand( 25, 100 );

    $timer->start();
    usleep( $duration );
    $timer->stop();

    $this->assertGreaterThanOrEqual( $duration, $timer->getDuration() );
  }

  /**
   * @depends testCanBeStartedAndStoppedWithDuration
   *
   * @covers \PhilKra\Helper\Timer::start
   * @covers \PhilKra\Helper\Timer::stop
   * @covers \PhilKra\Helper\Timer::getDuration
   * @covers \PhilKra\Helper\Timer::getElapsed
   * @covers \PhilKra\Helper\Timer::toMicro
   */
  public function testGetElapsedDurationWithoutError() {
    $timer = new Timer();

    $timer->start();
    usleep( 10 );
    $elapsed = $timer->getElapsed();
    $timer->stop();

    $this->assertGreaterThanOrEqual( $elapsed, $timer->getDuration() );
    $this->assertEquals( $timer->getElapsed(), $timer->getDuration() );
  }

  /**
   * @depends testCanBeStartedAndStoppedWithDuration
   *
   * @expectedException \PhilKra\Exception\Timer\NotStoppedException
   *
   * @covers \PhilKra\Helper\Timer::start
   * @covers \PhilKra\Helper\Timer::getDuration
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
   * @covers \PhilKra\Helper\Timer::stop
   */
  public function testCannotBeStoppedWithoutStart() {
    $timer = new Timer();
    $timer->stop();
  }

}
