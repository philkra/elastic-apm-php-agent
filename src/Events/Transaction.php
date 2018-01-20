<?php
namespace PhilKra\Events;

use \PhilKra\Helper\Timer;

/**
 *
 * Abstract Transaction class for all inheriting Transactions
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/transaction-api.html
 *
 */
class Transaction extends EventBean implements \JsonSerializable {

  /**
   * Transaction Name
   *
   * @var string
   */
  private $name;

  /**
   * Transaction Timer
   *
   * @var \PhilKra\Helper\Timer
   */
  private $timer;

  /**
   * Summary of this Transaction
   *
   * @var array
   */
  private $summary = [
    'duration'  => 0.0,
    'backtrace' => null,
    'headers'   => []
  ];

  /**
   * Transaction Metadata
   *
   * @var array
   */
  private $meta = [
    'result' => 200,
    'type'   => 'generic'
  ];

  /**
   * Extended Contexts such as Custom and/or User
   *
   * @var array
   */
  private $contexts = [
    'user'   => [],
    'custom' => [],
    'tags'   => []
  ];

  /**
   * Create the Transaction
   *
   * @param final string $name
   */
  public function __construct( string $name ) {
    parent::__construct();
    $this->setTransactionName( $name );
    $this->timer = new Timer();
  }

  /**
   * Start the Transaction
   *
   * @return void
   */
  public function start() {
    $this->timer->start();
  }

  /**
   * Stop the Transaction
   *
   * @return void
   */
  public function stop() {
    // Stop the Timer
    $this->timer->stop();

    // Store Summary
    $this->summary['duration']  = round( $this->timer->getDuration(), 3 );
    $this->summary['headers']   = xdebug_get_headers();
    $this->summary['backtrace'] = debug_backtrace();
  }

  /**
   * Set the Transaction Name
   *
   * @param string $name
   *
   * @return void
   */
  public function setTransactionName( string $name ) {
    $this->name = $name;
  }

  /**
   * Get the Transaction Name
   *
   * @return string
   */
  public function getTransactionName() : string {
    return $this->name;
  }

  /**
   * Get the Summary of this Transaction
   *
   * @return array
   */
  public function getSummary() : array {
    return $this->summary;
  }

  /**
   * Set the Transaction Meta data
   *
   * @param array $meta
   *
   * @return void
   */
  public function setMeta( array $meta ) {
    $this->meta = array_merge( $this->meta, $meta );
  }

  /**
   * Set Meta data of User Context
   *
   * @param array $userContext
   */
  public function setUserContext( array $userContext ) {
    $this->contexts['user'] = array_merge( $this->contexts['user'], $userContext );
  }

  /**
   * Set custom Meta data for the Transaction in Context
   *
   * @param array $customContext
   */
  public function setCustomContext( array $customContext ) {
    $this->contexts['custom'] = array_merge( $this->contexts['custom'], $customContext );
  }

  /**
   * Set Tags for this Transaction
   *
   * @param array $tags
   */
  public function setTags( array $tags ) {
    $this->contexts['tags'] = array_merge( $this->contexts['tags'], $tags );
  }

  /**
   * Serialize Transaction Event
   *
   * @return array
   */
  public function jsonSerialize() : array {
    return [
      'id'        => $this->getId(),
      'timestamp' => $this->getTimestamp(),
      'name'      => $this->getTransactionName(),
      'duration'  => $this->summary['duration'],
      'type'      => $this->meta['type'],
      'result'    => (string)$this->meta['result'],
      'context'   => $this->getContext(),
      'taces'     => $this->mapTraces(),
    ];
  }

  /**
   * Get the Transaction Context
   *
   * @link https://www.elastic.co/guide/en/apm/server/current/transaction-api.html#transaction-context-schema
   *
   * @return array
   */
  private function getContext() : array {
    $headers = getallheaders();

    // Build Context Stub
    $context = [
      'request' => [
        'http_version' => substr( $_SERVER['SERVER_PROTOCOL'], strpos( $_SERVER['SERVER_PROTOCOL'], '/' ) ),
        'method'       => $_SERVER['REQUEST_METHOD'],
        'socket'       => [
          'remote_address' => $_SERVER['REMOTE_ADDR'],
          'encrypted'      => isset( $_SERVER['HTTPS'] )
        ],
        'url'          => [
          'protocol' => isset( $_SERVER['HTTPS'] ) ? 'https' : 'http',
          'hostname' => $_SERVER['SERVER_NAME'],
          'port'     => $_SERVER['SERVER_PORT'],
          'pathname' => $_SERVER['SCRIPT_NAME'],
          'search'   => '?' . $_SERVER['QUERY_STRING']
        ],
        'headers' => [
          'user-agent' => $headers['User-Agent'],
          'cookie'     => $headers['Cookie']
        ],
        'env' => [
          'REMOTE_ADDR'     => $_SERVER['REMOTE_ADDR'],
          'REMOTE_PORT'     => $_SERVER['REMOTE_PORT'],
          'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],
          'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],
          'DOCUMENT_ROOT'   => $_SERVER['DOCUMENT_ROOT'],
        ]
      ]
    ];

    // Add Cookies Map
    if( empty( $_COOKIE ) === false ) {
      $context['request']['cookies'] = $_COOKIE;
    }

    // Add User Context
    if( empty( $this->contexts['user'] ) === false ) {
      $context['request']['user'] = $this->contexts['user'];
    }

    // Add Custom Context
    if( empty( $this->contexts['custom'] ) === false ) {
      $context['request']['custom'] = $this->contexts['custom'];
    }

    // Add Tags Context
    if( empty( $this->contexts['tags'] ) === false ) {
      $context['request']['tags'] = $this->contexts['tags'];
    }

    return $context;
  }

  private function mapTraces() : array {
    return [];
  }

}
