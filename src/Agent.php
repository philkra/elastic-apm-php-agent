<?php
namespace PhilKra;

use \PhilKra\Stores\ErrorsStore;
use \PhilKra\Stores\TransactionsStore;
use \PhilKra\Events\Transaction;
use \PhilKra\Events\Errors;
use \PhilKra\Helper\Timer;
use \PhilKra\Helper\Config;
use \PhilKra\Middleware\Connector;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use \PhilKra\Exception\Transaction\UnknownTransactionException;

/**
 *
 * APM Agent
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/transaction-api.html
 *
 */
class Agent {

  /**
   * Agent Version
   *
   * @var string
   */
  const VERSION = '0.1.1';

  /**
   * Agent Name
   *
   * @var string
   */
  const NAME = 'php';

  /**
   * Config Store
   *
   * @var \PhilKra\Helper\Config
   */
  private $config;

  /**
   * Transactions Store
   *
   * @var \PhilKra\Stores\TransactionsStore
   */
  private $transactionsStore;

  /**
   * Error Events Store
   *
   * @var \PhilKra\Stores\ErrorsStore
   */
  private $errorsStore;

  /**
   * Apm Timer
   *
   * @var \PhilKra\Helper\Timer
   */
  private $timer;

  /**
   * Setup the APM Agent
   *
   * @param array $config
   *
   * @return void
   */
  public function __construct( array $config ) {
    // Init Agent Config
    $this->config = new Config( $config );

    // Initialize Event Stores
    $this->transactionsStore = new TransactionsStore();
    $this->errorsStore       = new ErrorsStore();

    // Start Global Agent Timer
    $this->timer = new Timer();
    $this->timer->start();
  }

  /**
   * Start the Transaction capturing
   *
   * @throws \PhilKra\Exception\Transaction\DuplicateTransactionNameException
   *
   * @param string $name
   *
   * @return void
   */
  public function startTransaction( string $name ) {
    // Create and Store Transaction
    $this->transactionsStore->register( new Transaction( $name ) );

    // Start the Transaction
    $this->transactionsStore->fetch( $name )->start();
  }

  /**
   * Stop the Transaction
   *
   * @throws \PhilKra\Exception\Transaction\UnknownTransactionException
   *
   * @param string $name
   *
   * @return void
   */
  public function stopTransaction( string $name ) {
    // Does this Transaction even exist ?
    if( $this->transactionsStore->fetch( $name ) === null ) {
      throw new UnknownTransactionException( $name );
    }

    // Now, we're safe to stop it!
    $this->transactionsStore->fetch( $name )->stop();
  }

  /**
   * Get the Summary of a traced Transaction
   *
   * @param string $name
   *
   * @return mixed: \PhilKra\Transaction\Summary | null
   */
  public function getTransactionSummary( string $name ) {
    $trx = $this->transactionsStore->fetch( $name );
    return ( $trx === null ) ? null : $trx->getSummary();
  }

  /**
   * Register a Thrown Exception
   *
   * @link http://php.net/manual/en/class.throwable.php
   *
   * @param \Throwable $exception
   *
   * @return void
   */
  public function captureException( \Throwable $exception ) {
    $this->errorsStore->register( $this->timer->getElapsed(), $exception );
  }

  /**
   * Register a Thrown Error
   *
   * Mnemonic for @see self::captureException
   *
   * @param  Throwable $error
   *
   * @return void
   */
  public function captureError( \Throwable $error ) {
    $this->cacaptureException( $error );
  }

  /**
   * Get the Agent Config
   *
   * @return \PhilKra\Helper\Config
   */
  public function getConfig() : \PhilKra\Helper\Config {
    return $this->config;
  }

  /**
   * Send Data to APM Service
   *
   * @return bool
   */
  public function send() : bool {
    $connector = new Connector( $this->config );

    // Commit the Errors
    if( $this->errorsStore->isEmpty() === false ) {
      $connector->sendErrors( json_encode( $this->errorsStore ) );
    }

    // Commit the Transactions
    if( $this->transactionsStore->isEmpty() === false ) {
      $connector->sendTransactions( json_encode( $this->transactionsStore ) );
    }

    return true;
  }

}
