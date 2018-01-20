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
  const NAME = 'elastic-php';

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
   * Set Transaction Meta data
   *
   * @throws \PhilKra\Exception\Transaction\UnknownTransactionException
   *
   * @param string $name
   * @param array  $meta
   *
   * @return void
   */
  public function setTransactionMeta( string $name, array $meta ) {
    // Does this Transaction even exist ?
    if( $this->transactionsStore->fetch( $name ) === null ) {
      throw new UnknownTransactionException( $name );
    }

    // Now, we're safe to write the meta data!
    $this->transactionsStore->fetch( $name )->setMeta( $meta );
  }

  /**
   * Get a Transaction
   *
   * @param string $name
   *
   * @return void
   */
  public function getTransaction( string $name ) {
    return $this->transactionsStore->fetch( $name );
  }

  /**
   * Register a Thrown Exception, Error, etc.
   *
   * @link http://php.net/manual/en/class.throwable.php
   *
   * @param \Throwable $thrown
   *
   * @return void
   */
  public function captureThrowable( \Throwable $thrown ) {
    $this->errorsStore->register( $thrown );
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
    // Is the Agent enabled ?
    if( $this->config->get( 'active' ) === false ) {
      return false;
    }

    $connector = new Connector( $this->config );
    $status = true;

    // Commit the Errors
    if( $this->errorsStore->isEmpty() === false ) {
      $status = $status && $connector->sendErrors( $this->errorsStore );
    }

    // Commit the Transactions
    if( $this->transactionsStore->isEmpty() === false ) {
      $status = $status && $connector->sendTransactions( $this->transactionsStore );
    }

    return $status;
  }

}
