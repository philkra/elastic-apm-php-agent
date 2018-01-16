<?php
namespace PhilKra\ElasticApmAgent;

use \PhilKra\Instrumentation\Timer;

/**
 *
 */
class Apm {

  /**
   * Config Store
   *
   * @var array
   */
  private array $config = [];

  /**
   * Capturing started bit
   *
   * @var bool
   */
  private bool $started = false;

  /**
   * Process Timer
   *
   * @var \PhilKra\Instrumentation\Timer
   */
  private final Timer $timer;

  /**
   * Setup the APM Agent
   *
   * @param array $config, Default: []
   *
   * @return void
   */
  public function __construct( array $config = [] ) : void {
    $this->config = array_merge( $this->getDefaultConfig(), $config );
    $this->timer  = new Timer();
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
   * Start the Agent capturing
   *
   * @return void
   */
  public function start() : void {
    $this->started = true;
    $this->timer->start();
  }

  /**
   * Get the Default Config of the Agent
   *
   * @return array
   */
  private function getDefaultConfig() : array {
    return [
      'appName'     => getenv( 'HTTP_HOST' ),
      'secretToken' => null,
      'serverUrl'   => 'http://127.0.0.1:8200'
    ];
  }

}
