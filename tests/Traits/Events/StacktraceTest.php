<?php
namespace PhilKra\Tests\Traits\Events;

use \PhilKra\Traits\Events\Stacktrace;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use PhilKra\Tests\PHPUnitUtils;

/**
 * Test Case for @see \PhilKra\Traits\Events\Stacktrace
 */
final class StacktraceTest extends TestCase {

    /** @var Stacktrace|PHPUnit_Framework_MockObject_MockObject */
    private $stacktraceMock;

    protected function setUp()
    {
        $this->stacktraceMock = $this->getMockForTrait(Stacktrace::class);
    }

    protected function tearDown()
    {
        $this->stacktraceMock = null;
    }

    /**
     * @covers \PhilKra\Traits\Events\Stacktrace::getDebugBacktrace
     */
    public function testEntry()
    {
        $n = rand(4, 7);
        $result = PHPUnitUtils::callMethod($this->stacktraceMock, 'getDebugBacktrace', [$n]);

        // Ensure the first elem is not present (self)
        $this->assertEquals(count($result), ($n - 1));

        $this->assertArrayHasKey('abs_path', $result[0]);
        $this->assertArrayHasKey('filename', $result[0]);
        $this->assertArrayHasKey('function', $result[0]);
        $this->assertArrayHasKey('lineno', $result[0]);
        $this->assertArrayHasKey('module', $result[0]);
        $this->assertArrayHasKey('vars', $result[0]);
        $this->assertArrayHasKey(1, $result[0]['vars']);

        $this->assertStringEndsWith('tests/PHPUnitUtils.php', $result[0]['abs_path']);
        $this->assertEquals('PHPUnitUtils.php', $result[0]['filename']);
        $this->assertEquals('invokeArgs', $result[0]['function']);
        $this->assertEquals(16, $result[0]['lineno']);
        $this->assertEquals('ReflectionMethod', $result[0]['module']);
        $this->assertEquals($n, $result[0]['vars'][1][0]);

        $this->assertStringEndsWith('tests/Traits/Events/StacktraceTest.php', $result[1]['abs_path']);
        $this->assertEquals('StacktraceTest.php', $result[1]['filename']);
        $this->assertEquals('callMethod', $result[1]['function']);
        $this->assertEquals(33, $result[1]['lineno']);
        $this->assertEquals('PhilKra\Tests\PHPUnitUtils', $result[1]['module']);
    }

}
