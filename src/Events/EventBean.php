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
   * Init the Event with the Timestamp and UUID
   */
  public function __construct() {
    $this->id = Uuid::uuid4()->toString();

    $timestamp = \DateTime::createFromFormat( 'U.u', microtime( true ) );
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

}
