<?php
namespace PhilKra\Exception\Timer;

/**
 * Trying to stop a Timer that is already running
 */
class AlreadyRunningException extends \Exception {

  public function __construct( string $message = '', int $code = 0, \Throwable $previous = NULL ) {
    parent::__construct( 'Can\'t start a timer which is already running.', $code, $previous );
  }

}
