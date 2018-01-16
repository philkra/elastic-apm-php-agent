<?php
namespace PhilKra;

use \PhilKra\Transaction\Store;
use \PhilKra\Transaction\Factory;
use \PhilKra\Transaction\ITransaction;
use \PhilKra\Exception\MissingAppNameException;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;

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
   * Setup the APM Agent
   *
   * @param array $config, Default: []
   *
   * @return void
   */
  public function __construct( array $config ) {
    if( isset( $config['appName'] ) === false ) {
      throw new MissingAppNameException();
    }

    $this->transactions = new Store();
    $this->config = array_merge( $this->getDefaultConfig(), $config );
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
