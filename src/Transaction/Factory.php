<?php
namespace PhilKra\Transaction;

use \PhilKra\Transaction\Transaction;
use \PhilKra\Transaction\ITransaction;

/**
 * Factory that creates the Transaction class
 */
class Factory {

  /**
   * Create a Transaction
   *
   * @param  string $transactionName
   *
   * @return ITransaction
   */
  public static function create( string $transactionName ) : ITransaction {
    return new Transaction( $transactionName );
  }

}
