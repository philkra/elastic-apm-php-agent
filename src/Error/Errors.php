<?php
namespace PhilKra\Error;

/**
 * Registry for captured the Errors/Excpetions
 */
class Errors {

  /**
   * Set of Errors
   *
   * @var array of Throwable
   */
  private $store = [];

  /**
   * Register an Error
   *
   * @param  float     $occurredAfter
   * @param  Throwable $error
   *
   * @return void
   */
  public function register( float $occurredAfter, Throwable $error ) {
    array_push( $this->store, new Bean( $occurredAfter, $error ) );
  }

  /**
   * Get all Registered Errors
   *
   * @return array
   */
  public function list() : array {
    return $this->store;
  }

}
