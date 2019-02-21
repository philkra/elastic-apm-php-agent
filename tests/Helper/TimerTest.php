<?php
namespace PhilKra\Tests\Helper;

use PhilKra\Exception\Timer\AlreadyRunningException;
use \PhilKra\Helper\Timer;
use PhilKra\Tests\TestCase;

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
     * @covers \PhilKra\Helper\Timer::start
     * @covers \PhilKra\Helper\Timer::stop
     * @covers \PhilKra\Helper\Timer::getDuration
     * @covers \PhilKra\Helper\Timer::toMicro
     */
    public function testCanCalculateDurationInMilliseconds() {
        $timer = new Timer();
        $duration = rand( 25, 100 ); // duration in milliseconds

        $timer->start();
        usleep( $duration * 1000 ); // sleep microseconds
        $timer->stop();

        $this->assertDurationIsWithinThreshold($duration, $timer->getDurationInMilliseconds());
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
   * @covers \PhilKra\Helper\Timer::start
   * @covers \PhilKra\Helper\Timer::getDuration
   */
  public function testCanBeStartedWithForcingDurationException() {
    $timer = new Timer();
    $timer->start();

    $this->expectException( \PhilKra\Exception\Timer\NotStoppedException::class );

    $timer->getDuration();
  }

  /**
   * @depends testCanBeStartedWithForcingDurationException
   *
   * @covers \PhilKra\Helper\Timer::stop
   */
  public function testCannotBeStoppedWithoutStart() {
    $timer = new Timer();

    $this->expectException( \PhilKra\Exception\Timer\NotStartedException::class );

    $timer->stop();
  }

    /**
     * @covers \PhilKra\Helper\Timer::start
     * @covers \PhilKra\Helper\Timer::getDurationInMilliseconds
     */
    public function testCanBeStartedWithExplicitStartTime() {
        $timer = new Timer(microtime(true) - .5); // Start timer 500 milliseconds ago

        usleep(500 * 1000); // Sleep for 500 milliseconds

        $timer->stop();

        $duration = $timer->getDurationInMilliseconds();

        // Duration should be more than 1000 milliseconds
        //  sum of initial offset and sleep
        $this->assertGreaterThanOrEqual(1000, $duration);
    }

    /**
     * @covers \PhilKra\Helper\Timer::start
     */
    public function testCannotBeStartedIfAlreadyRunning() {
        $timer = new Timer(microtime(true));

        $this->expectException(AlreadyRunningException::class);
        $timer->start();
    }
}
