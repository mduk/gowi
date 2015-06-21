<?php

namespace Mduk\Gowi\Service;

use Mduk\Gowi\Collection;

use Mduk\Gowi\Service;
use Mduk\Gowi\Service\Request as ServiceRequest;
use Mduk\Gowi\Service\Response as ServiceResponse;

class Shim implements Service {

  protected $description;

  public function __construct( $description ) {
    $this->description = $description;
  }

  public function describe() {
    $calls = [];
    foreach ( $this->calls as $call => $spec ) {
      $calls[ $call ] => [
        'description' => $spec['description']
      ];
    }

    return [
      'description' => $this->description,
      'calls' => $calls
    ];
  }

  public function request( $call ) {
    return new ServiceRequest( $this, $call );
  }

  public function execute( ServiceRequest $request, ServiceResponse $response ) {
    $call = $request->getCall();

    if ( !isset( $this->calls[ $call ] ) ) {
      throw new \Exception( "Invalid call: {$call}" );
    }

    $call = $this->calls[ $call ];

    $callback = $call->callback;
    $args = $this->getArgs( $request, $call->arguments );

    $result = call_user_func_array( $callback, $args );

    if ( is_array( $result ) ) {
      $isAssoc = ( array_keys( $result ) !== range( 0, count( $result ) - 1 ) );

      if ( !$isAssoc ) {
        $results = new Collection( $result );
      }
      else {
        $results = new Collection;
        $results[] = $result;
      }
    }
    else {
      $results = new Collection;
      $results[] = $result;
    }

    $response->setResults( $results );

    return $response;
  }

  public function setCall( $call, $callback, $arguments, $description ) {
    $this->calls[ $call ] = (object) [
      'callback' => $callback,
      'arguments' => $arguments,
      'description' => $description
    ];
  }

  protected function getArgs( $request, $argList ) {
    $args = [];
    foreach ( $argList as $arg ) {
      if ( $arg == '__payload' ) {
        $args[] = $request->getPayload();
        continue;
      }

      $args[] = $request->getParameter( $arg );
    }
    return $args;
  }
}
