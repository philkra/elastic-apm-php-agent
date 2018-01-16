<?php
namespace PhilKra\Transaction;

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

}
