<?php

namespace PhilKra\Tests;

use PhilKra\TraceParent;

/**
 * Test Case for @see \PhilKra\TraceParent
 */
final class TraceParentTest extends TestCase {

    /**
     * @covers \PhilKra\TraceParent::__construct
     * @covers \PhilKra\TraceParent::isValidHeader
     * @covers \PhilKra\TraceParent::createFromHeader
     * @covers \PhilKra\TraceParent::__toString
     */
    public function testCanCreateFromValidHeader() {
        $header = "00-0bfda6be83a31fb66a455cbb74a70344-6b84fae6bd7064af-01";
        $traceParent = TraceParent::createFromHeader($header);

        $this->assertEquals("0bfda6be83a31fb66a455cbb74a70344", $traceParent->getTraceId());
        $this->assertEquals("6b84fae6bd7064af", $traceParent->getParentId());
        $this->assertEquals("01", $traceParent->getTraceFlags());
        $this->assertEquals($header, $traceParent->__toString());
    }

    /**
     * @covers \PhilKra\TraceParent::isValidHeader
     * @covers \PhilKra\TraceParent::createFromHeader
     */
    public function testCannotCreateFromInvalidHeader() {
        $invalidHeaders = [
            "",
            "118c6c15301b9b3b3:56e66177e6e55a91:18c6c15301b9b3b3:1"
        ];

        $this->expectException( \PhilKra\Exception\InvalidTraceContextHeaderException::class );

        foreach ($invalidHeaders as $header) {
            TraceParent::createFromHeader($header);
        }
    }
}