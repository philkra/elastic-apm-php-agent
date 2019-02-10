<?php
namespace PhilKra\Tests\Stores;

use \PhilKra\Stores\ErrorsStore;
use \PhilKra\Events\Error;
use PhilKra\Tests\TestCase;

/**
 * Test Case for @see \PhilKra\Stores\ErrorsStore
 */
final class ErrorsStoreTest extends TestCase {

  /**
   * @covers \PhilKra\Stores\ErrorsStoreTest::register
   * @covers \PhilKra\Stores\ErrorsStoreTest::list
   */
  public function testCaptureErrorExceptionAndListIt() {
    $store = new ErrorsStore();
    $error = new Error( new \Error( 'unit-test' ), [] );

    // Must be Empty
    $this->assertTrue( $store->isEmpty() );

    // Store the Error and Check that it's "stored"
    $store->register( $error );
    $list = $store->list();

    $this->assertEquals( count( $list ), 1 );

    // Must not be Empty
    $this->assertFalse( $store->isEmpty() );
  }

}
