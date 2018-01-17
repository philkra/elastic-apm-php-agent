<?php
namespace PhilKra\Serializers;

use \PhilKra\Transaction\ITransaction;

/**
 *
 * Convert the Registered Transactions to JSON Schema
 *
 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
 *
 */
class Transactions implements JsonSerializable {

  /**
   * @var \PhilKra\Transactions\Store
   */
  private $transactions;

  /**
   * @param Config $config
   * @param Store  $transactions
   */
  public function __construct( Config $config, Store $transactions ) {
    parent::__construct( $config );
    $this->transactions = $transactions;
  }

  /**
   * Serialize Error Data to JSON "ready" Array
   *
   * @return array
   */
  public function jsonSerialize() {
    $set = $this->getSkeleton();

    $set += [
      'transactions' => [

      ]
    ];

    return $set;
  }

}
