<?php
namespace PhilKra\Serializers;

use \PhilKra\Stores\TransactionsStore;

/**
 *
 * Convert the Registered Transactions to JSON Schema
 *
 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
 *
 */
class Transactions extends Entity implements \JsonSerializable {

  /**
   * @var \PhilKra\Stores\TransactionsStore
   */
  private $store;

  /**
   * @param ErrorsStore $store
   */
  public function __construct( TransactionsStore $store ) {
    $this->$store = $store;
  }

  /**
   * Serialize Transactions Data to JSON "ready" Array
   *
   * @return array
   */
  public function jsonSerialize() {
    return $this->getSkeleton() + [
      'transactions' => $this->store
    ];
  }

}
