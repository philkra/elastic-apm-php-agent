<?php
namespace PhilKra;

use \PhilKra\Transaction\Store;
use \PhilKra\Transaction\Factory;
use \PhilKra\Transaction\ITransaction;
use \PhilKra\Helper\Timer;
use \PhilKra\Helper\Config;
use \PhilKra\Error\Errors;
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
  const VERSION = '0.1.0';

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
   * @var \PhilKra\Transaction\Store
   */
  private $transactions;

  /**
   * Apm Timer
   *
   * @var \PhilKra\Helper\Timer
   */
  private $timer;

  /**
   * Errors Registry
   *
   * @var \PhilKra\Error\Errors
   */
  private $errors;

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

    // Prepare Transactions DataStore
    $this->transactions = new Store();

    // Initialize the Error Registry
    $this->errors = new Errors();

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
    $this->transactions->register( Factory::create( $name ) );

    // Start the Transaction
    $this->transactions->fetch( $name )->start();
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
    if( $this->transactions->fetch( $name ) === null ) {
      throw new UnknownTransactionException( $name );
    }

    // Now, we're safe to stop it!
    $this->transactions->fetch( $name )->stop();
  }

  /**
   * Get the Summary of a traced Transaction
   *
   * @param string $name
   *
   * @return mixed: \PhilKra\Transaction\Summary | null
   */
  public function getTransactionSummary( string $name ) {
    $trx = $this->transactions->fetch( $name );
    return ( $trx === null ) ? null : $trx->getSummary();
  }

  /**
   * Register a Thrown Error/Exception
   *
   * @link http://php.net/manual/en/class.throwable.php
   *
   * @param Throwable $exception
   *
   * @return void
   */
  public function captureException( Throwable $exception ) {
    $this->errors->register( $this->timer->getElapsed(), $exception );
  }

  /**
   * Get the Agent Config
   *
   * @return PhilKraHelperConfig
   */
  public function getConfig() : \PhilKra\Helper\Config {
    return $this->config;
  }

}
