<?php
namespace PhilKra\Tests\Helper;

use \PhilKra\Helper\StackTrace;
use PhilKra\Tests\TestCase;

/**
 * Test Case for @see \PhilKra\Helper\StackTrace
 */
final class StackTraceTest extends TestCase {

    /**
     * @covers \PhilKra\Helper\StackTrace::convertBacktraceToStackFrames
     */
    public function testSingleEntry() {

        $bt['file'] = '/some/directory/some_file.php';
        $bt['function'] = 'a_function_name';
        $bt['line'] = 69;
        $bt['class'] = 'a_class_name';
        $bt['args'] = ['arg1', 'arg2'];
        $input[] = $bt;


        $trace['abs_path'] = '/some/directory/some_file.php';
        $trace['filename'] = 'some_file.php';
        $trace['function'] = 'a_function_name';
        $trace['lineno']   = 69;
        $trace['module']   = 'a_class_name';
        $trace['vars']     = ['arg1', 'arg2'];
        $output[] = $trace;

        $this->assertEquals( $output, StackTrace::convertBacktraceToStackFrames($input) );

    }

    /**
     * @covers \PhilKra\Helper\StackTrace::convertBacktraceToStackFrames
     */
    public function testMultipleEntries() {


        $bt1['file'] = '/some/directory/some_file.php';
        $bt1['function'] = 'a_function_name';
        $bt1['line'] = 69;
        $bt1['class'] = 'a_class_name';
        $bt1['args'] = ['arg1', 'arg2'];
        $bt2['file'] = '/some/directory/some_other_file.php';
        $bt2['function'] = 'another_function_name';
        $bt2['line'] = 73;
        $bt2['class'] = 'some_other_class_name';
        $bt2['args'] = ['arg3', 'arg4'];
        $input[] = $bt1;
        $input[] = $bt2;


        $trace1['abs_path'] = '/some/directory/some_file.php';
        $trace1['filename'] = 'some_file.php';
        $trace1['function'] = 'a_function_name';
        $trace1['lineno']   = 69;
        $trace1['module']   = 'a_class_name';
        $trace1['vars']     = ['arg1', 'arg2'];

        $trace2['abs_path'] = '/some/directory/some_other_file.php';
        $trace2['filename'] = 'some_other_file.php';
        $trace2['function'] = 'another_function_name';
        $trace2['lineno']   = 73;
        $trace2['module']   = 'some_other_class_name';
        $trace2['vars']     = ['arg3', 'arg4'];
        $output[] = $trace1;
        $output[] = $trace2;

        $this->assertEquals( $output, StackTrace::convertBacktraceToStackFrames($input) );

    }

}
