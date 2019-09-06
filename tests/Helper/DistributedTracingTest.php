<?php

namespace PhilKra\Tests\Helper;

use PhilKra\Helper\DistributedTracing;
use PhilKra\Tests\TestCase;

/**
 * Test Case for @see \PhilKra\Helper\DistributedTracing
 */
final class DistributedTracingTest extends TestCase {

    /**
     * @covers \PhilKra\Helper\DistributedTracing::__construct
     * @covers \PhilKra\Helper\DistributedTracing::isValidHeader
     * @covers \PhilKra\Helper\DistributedTracing::createFromHeader
     * @covers \PhilKra\Helper\DistributedTracing::__toString
     */
    public function testCanCreateFromValidHeader() {
        $header = "00-0bfda6be83a31fb66a455cbb74a70344-6b84fae6bd7064af-01";
        $traceParent = DistributedTracing::createFromHeader($header);

        $this->assertEquals("0bfda6be83a31fb66a455cbb74a70344", $traceParent->getTraceId());
        $this->assertEquals("6b84fae6bd7064af", $traceParent->getParentId());
        $this->assertEquals("01", $traceParent->getTraceFlags());
        $this->assertEquals($header, $traceParent->__toString());
    }

    /**
     * @covers \PhilKra\Helper\DistributedTracing::isValidHeader
     * @covers \PhilKra\Helper\DistributedTracing::createFromHeader
     */
    public function testCannotCreateFromInvalidHeader() {
        $invalidHeaders = [
            "",
            "118c6c15301b9b3b3:56e66177e6e55a91:18c6c15301b9b3b3:1"
        ];

        $this->expectException( \PhilKra\Exception\InvalidTraceContextHeaderException::class );

        foreach ($invalidHeaders as $header) {
            DistributedTracing::createFromHeader($header);
        }
    }
}