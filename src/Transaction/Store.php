<?php
namespace PhilKra\Transaction;

use \PhilKra\Transaction\ITransaction;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;

/**
 * Store for the Transactions
 */
class Store {

  /**
   * HashMap of Transactions
   *
   * @var array of \PhilKra\Transaction\ITransaction
   */
  private $store = [];

  /**
   * Register a Transaction
   *
   * @throws \PhilKra\Exception\Transaction\DuplicateTransactionNameException
   *
   * @param \PhilKra\Transaction\ITransaction $transaction
   *
   * @return void
   */
  public function register( ITransaction $transaction ) {
    $name = $transaction->getTransactionName();

    // Do not override the
    if( isset( $this->store[$name] ) === true ) {
      throw new DuplicateTransactionNameException( $name );
    }

    // Push to Store
    $this->store[$name] = $transaction;
  }

  /**
   * Fetch a Transaction from the Store
   *
   * @param final string $name
   *
   * @return mixed: \PhilKra\Transaction\ITransaction | null
   */
  public function fetch( string $name ) {
    return $this->store[$name] ?? null;
  }

}
