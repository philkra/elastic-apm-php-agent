<?php
namespace PhilKra\Transaction;

use \PhilKra\Transaction\Summary;

/**
 * Interface for Transactions
 */
interface ITransaction {

  /**
   * Start the Transaction
   *
   * @return void
   */
  public function start();

  /**
   * Stop the Transaction
   *
   * @return void
   */
  public function stop();

  /**
   * Set the Transaction Name
   *
   * @param string $name
   *
   * @return void
   */
  public function setTransactionName( string $name );

  /**
   * Get the Transaction Name
   *
   * @return string
   */
  public function getTransactionName() : string;

  /**
   * Get the Transaction's Summary
   *
   * @return \PhilKra\Transaction\Summary
   */
  public function getSummary() : Summary;

}
