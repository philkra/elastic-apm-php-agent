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

    protected function assertDurationIsWithinThreshold(int $expectedMilliseconds, float $timedDuration, float $maxOverhead = 10)
    {
        $this->assertGreaterThanOrEqual( $expectedMilliseconds, $timedDuration );

        $overhead = ($timedDuration - $expectedMilliseconds);
        $this->assertLessThanOrEqual( $maxOverhead, $overhead );
    }

}