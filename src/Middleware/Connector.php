<?php
namespace PhilKra\Middleware;

use \PhilKra\Agent;
use \GuzzleHttp\Psr7\Request;
use \PhilKra\Stores\ErrorsStore;
use \PhilKra\Stores\TransactionsStore;
use \PhilKra\Serializers\Errors;
use \PhilKra\Serializers\Transactions;

/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
class Connector {

  /**
   * Agent Config
   *
   * @var \PhilKra\Helper\Config
   */
  private $config;

  /**
   * @param \PhilKra\Helper\Config $config
   */
  public function __construct( \PhilKra\Helper\Config $config ) {
    $this->config = $config;
  }

  /**
   * Push the Transactions to APM Server
   *
   * @param \PhilKra\Stores\TransactionsStore $store
   *
   * @return
   */
  public function sendTransactions( TransactionsStore $store ) {
    $request = new Request(
      'POST',
      $this->getEndpoint( 'transactions' ),
      $this->getRequestHeaders(),
      json_encode( new Transactions( $this->config, $store ) )
    );
  }

  /**
   * Push the Errors to APM Server
   *
   * @param \PhilKra\Stores\ErrorsStore $store
   *
   * @return
   */
  public function sendErrors( ErrorsStore $store ) {
    $request = new Request(
      'POST',
      $this->getEndpoint( 'errors' ),
      $this->getRequestHeaders(),
      json_encode( new Errors( $this->config, $store ) )
    );
  }

  /**
   * Get the Endpoint URI of the APM Server
   *
   * @param string $endpoint
   *
   * @return string
   */
  private function getEndpoint( string $endpoint ) : string {
    return sprintf(
      '%s/%s/%s',
      $this->config->get( 'serverUrl' ),
      $this->config->get( 'apmVersion' ),
      $endpoint
    );
  }

  /**
   * Get the Headers for the POST Request
   *
   * @return array
   */
  private function getRequestHeaders() : array {
    // Default Headers Set
    $headers = [
      'Content-Type'     => 'application/json',
      'Content-Encoding' => 'deflate',
      'User-Agent'       => sprintf( 'elasticapm-php/%s', Agent::VERSION ),
    ];

    // Add Secret Token to Header
    if( $this->config->get( 'secretToken' ) !== null ) {
      $headers['Authorization'] = sprintf( 'Bearer %s', $this->config['secretToken'] );
    }

    return $headers;
  }

}
