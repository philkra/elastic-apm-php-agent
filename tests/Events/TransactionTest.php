<?php
namespace PhilKra\Tests\Events;

use \PhilKra\Events\Transaction;
use PhilKra\Tests\TestCase;

/**
 * Test Case for @see \PhilKra\Events\Transaction
 */
final class TransactionTest extends TestCase {

    /**
     * @covers \PhilKra\Events\EventBean::getId
     * @covers \PhilKra\Events\EventBean::getTraceId
     * @covers \PhilKra\Events\Transaction::getTransactionName
     * @covers \PhilKra\Events\Transaction::setTransactionName
     */
    public function testParentConstructor() {
        $now = microtime(true) * 1000000;

        $name = 'testerus-grandes';
        $transaction = new Transaction($name, []);

        $this->assertEquals($name, $transaction->getTransactionName());
        $this->assertNotNull($transaction->getId());
        $this->assertNotNull($transaction->getTraceId());
        $this->assertNotNull($transaction->getTimestamp());
        $this->assertGreaterThanOrEqual($transaction->getTimestamp(), $now);
    }

    /**
     * @depends testParentConstructor
     *
     * @covers \PhilKra\Events\EventBean::setParent
     * @covers \PhilKra\Events\EventBean::getTraceId
     * @covers \PhilKra\Events\EventBean::ensureGetTraceId
     */
    public function testParentReference() {
        $parent = new Transaction('parent', []);
        $child  = new Transaction('child', []);
        $child->setParent($parent);

        $arr = json_decode(json_encode($child), true);

        $this->assertEquals($arr['id'], $child->getId());
        $this->assertEquals($arr['parent_id'], $parent->getId());
        $this->assertEquals($arr['trace_id'], $parent->getTraceId());
        $this->assertEquals($child->getTraceId(), $parent->getTraceId());
    }

}
