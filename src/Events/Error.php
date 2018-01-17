<?php
namespace PhilKra\Events;

/**
 *
 * Event Bean for Error wrapping
 *
 */
class Error extends EventBean implements \JsonSerializable {

  /**
   * Error | Exception
   *
   * @link http://php.net/manual/en/class.throwable.php
   *
   * @var Throwable
   */
  private $throwable;

  /**
   * @param Throwable $throwable
   */
  public function __construct( \Throwable $throwable ) {
    parent::__construct();
    $this->throwable = $throwable;
  }

  /**
   * Serialize Event
   *
   * @return array
   */
  public function jsonSerialize() : array {
    return [
      'id'        => $this->getId(),
      'timestamp' => $this->getTimestamp(),
      'exception' => [
        'message'    => $this->throwable->getMessage(),
//        'type'       => $this->throwable->getType(),
        'code'       => $this->throwable->getCode(),
        'stacktrace' => $this->mapStacktrace(),
      ]
    ];
  }

  /**
   * Map the Stacktrace to Schema
   *
   * @return array
   */
  private function mapStacktrace() : array {
    $stacktrace = [];

    foreach( $this->throwable->getTrace() as $trace ) {
      $item = [
        'function' => $trace['function']
      ];
      if( isset( $trace['line'] ) === true ) {
        $item['lineno'] = $trace['line'];
      }
      if( isset( $trace['file'] ) === true ) {
        $item += [
          'abs_path' => basename( $trace['file'] ),
          'filename' => $trace['file']
        ];
      }
      if( isset( $trace['class'] ) === true ) {
        $item['module'] = $trace['class'];
      }
      if( isset( $trace['type'] ) === true ) {
        $item['type'] = $trace['type'];
      }

      array_push( $stacktrace, $item );
    }

    return $stacktrace;
  }

}
