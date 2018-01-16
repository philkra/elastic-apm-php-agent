<?php
namespace PhilKra\Tests\Transaction;

use \PhilKra\Transaction\Store;
use \PhilKra\Transaction\ITransaction;
use \PhilKra\Transaction\Factory;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use \PHPUnit\Framework\TestCase;

/**
 * Test Case for @see \PhilKra\Transaction\Store
 */
final class StoreTest extends TestCase {

  /**
   * @covers \PhilKra\Transaction\Store::register
   * @covers \PhilKra\Transaction\Store::get
   */
  public function testTransactionRegistrationAndFetch() {
    $store = new Store();
    $name  = 'test';
    $trx   = Factory::create( $name );

    // Store the Transaction and fetch it then
    $store->register( $trx );
    $proof = $store->fetch( $name );

    // We should get the Same!
    $this->assertEquals( $trx, $proof );
    $this->assertNotNull( $proof );
  }

  /**
   * @depends testTransactionRegistrationAndFetch
   *
   * @expectedException \PhilKra\Exception\Transaction\DuplicateTransactionNameException
   *
   * @covers \PhilKra\Transaction\Store::register
   */
  public function testDuplicateTransactionRegistration() {
    $store = new Store();
    $name  = 'test';
    $trx   = Factory::create( $name );

    // Store the Transaction again to force an Exception
    $store->register( $trx );
    $store->register( $trx );
  }

  /**
   * @depends testTransactionRegistrationAndFetch
   *
   * @covers \PhilKra\Transaction\Store::get
   */
  public function testFetchUnknownTransaction() {
    $store = new Store();
    $this->assertNull( $store->fetch( 'unknown' ) );
  }

}
