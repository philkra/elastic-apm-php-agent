<?php
namespace PhilKra\Stores;

use \PhilKra\Events\Error;

/**
 *
 * Registry for captured the Errors/Excpetions
 *
 */
class ErrorsStore implements \JsonSerializable {

  /**
   * Set of Errors
   *
   * @var array of \PhilKra\Events\Error
   */
  private $store = [];

  /**
   * Register an Error
   *
   * @param \PhilKra\Events\Error $error
   *
   * @return void
   */
  public function register( \PhilKra\Events\Error $error ) {
    array_push( $this->store, $error );
  }

  /**
   * Get all Registered Errors
   *
   * @return array of \PhilKra\Events\Error
   */
  public function list() : array {
    return $this->store;
  }

  /**
   * Is the Store Empty ?
   *
   * @return bool
   */
  public function isEmpty() : bool {
    return empty( $this->store );
  }

  /**
   * Serialize the Error Events Store
   *
   * @return array
   */
  public function jsonSerialize() : array {
    return $this->store;
  }

}
