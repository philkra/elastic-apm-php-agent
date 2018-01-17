<?php
namespace PhilKra;

use \PhilKra\Transaction\Store;
use \PhilKra\Transaction\Factory;
use \PhilKra\Transaction\ITransaction;
use \PhilKra\Helper\Timer;
use \PhilKra\Exception\MissingAppNameException;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use \PhilKra\Exception\Transaction\UnknownTransactionException;

/**
 * APM Agent
 */
class Apm {

  /**
   * Config Store
   *
   * @var array
   */
  private $config = [];

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
   * Setup the APM Agent
   *
   * @param array $config
   *
   * @return void
   */
  public function __construct( array $config ) {
    if( isset( $config['appName'] ) === false ) {
      throw new MissingAppNameException();
    }

    // Register Merged Config
    $this->config = array_merge( $this->getDefaultConfig(), $config );

    // Prepare Transactions DataStore
    $this->transactions = new Store();

    // Start Global Agent Timer
    $this->timer = new Timer();
    $this->timer->start();
  }

  /**
   * Get the Config
   *
   * @return array
   */
  public function getConfig() : array {
    return $this->config;
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
   * Get the Default Config of the Agent
   *
   * @return array
   */
  private function getDefaultConfig() : array {
    return [
      'secretToken' => null,
      'serverUrl'   => 'http://127.0.0.1:8200',
      'hostname'    => gethostname(),
      'timeout'     => 5,
    ];
  }

}
