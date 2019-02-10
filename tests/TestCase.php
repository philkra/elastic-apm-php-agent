<?php
/**
 * Created by PhpStorm.
 * User: tepeds
 * Date: 2019-02-09
 * Time: 10:49
 */

namespace PhilKra\Tests;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    protected function assertDurationIsWithinThreshold(int $sleptMilliseconds, float $timedDuration)
    {
        $this->assertGreaterThanOrEqual( $sleptMilliseconds, $timedDuration );

        // Generally we should expect less than 1ms of overhead, but that is not guaranteed.
        // 10ms should be enough unless the test system is really unresponsive.
        $overhead = ($timedDuration - $sleptMilliseconds);
        $this->assertLessThanOrEqual( 10, $overhead );
    }

}