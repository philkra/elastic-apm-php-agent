<?php
namespace PhilKra\Events;

use \Ramsey\Uuid\Uuid;

/**
 *
 * EventBean for occuring events such as Excpetions or Transactions
 *
 */
class EventBean {

  /**
   * UUID
   *
   * @var string
   */
  private $id;

  /**
   * Error occurred on Timestamp
   *
   * @var string
   */
  private $timestamp;

  /**
   * Event Metadata
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
   * Init the Event with the Timestamp and UUID
   *
   * @link https://github.com/philkra/elastic-apm-php-agent/issues/3
   */
  public function __construct() {
    $this->id = Uuid::uuid4()->toString();

    $timestamp = \DateTime::createFromFormat( 'U.u', sprintf("%.6F", microtime( true ) ) );
    $timestamp->setTimeZone( new \DateTimeZone( 'UTC' ) );
    $this->timestamp = $timestamp->format( 'Y-m-d\TH:i:s.u\Z' );
  }

  /**
   * Get the Event Id
   *
   * @return string
   */
  public function getId() : string {
    return $this->id;
  }

  /**
   * Get the Event's Timestamp
   *
   * @return string
   */
  public function getTimestamp() : string {
    return $this->timestamp;
  }

  /**
   * Set the Transaction Meta data
   *
   * @param array $meta
   *
   * @return void
   */
  public final function setMeta( array $meta ) {
    $this->meta = array_merge( $this->meta, $meta );
  }

  /**
   * Set Meta data of User Context
   *
   * @param array $userContext
   */
  public final function setUserContext( array $userContext ) {
    $this->contexts['user'] = array_merge( $this->contexts['user'], $userContext );
  }

  /**
   * Set custom Meta data for the Transaction in Context
   *
   * @param array $customContext
   */
  public final function setCustomContext( array $customContext ) {
    $this->contexts['custom'] = array_merge( $this->contexts['custom'], $customContext );
  }

  /**
   * Set Tags for this Transaction
   *
   * @param array $tags
   */
  public final function setTags( array $tags ) {
    $this->contexts['tags'] = array_merge( $this->contexts['tags'], $tags );
  }

  /**
   * Get Type defined in Meta
   *
   * @return string
   */
  protected final function getMetaType() : string {
    return $this->meta['type'];
  }

  /**
   * Get the Result of the Event from the Meta store
   *
   * @return string
   */
  protected final function getMetaResult() : string {
    return (string)$this->meta['result'];
  }

  /**
   * Get the Events Context
   *
   * @link https://www.elastic.co/guide/en/apm/server/current/transaction-api.html#transaction-context-schema
   *
   * @return array
   */
  protected final function getContext() : array {
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
          'search'   => '?' . ( ( $_SERVER['QUERY_STRING'] ) ?? '' )
        ],
        'headers' => [
          'user-agent' => $headers['User-Agent'] ?? '',
          'cookie'     => $headers['Cookie'] ?? ''
        ],
        'env' => $_SERVER,
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

}
