<?php

namespace Mduk\Gowi\Service;

use Mduk\Gowi\Service;

class Request {
  protected $service;
  protected $call;
  protected $payload;
  protected $parameters = [];

  public function __construct( Service $service, $call ) {
    $this->service = $service;
    $this->call = $call;
  }

  public function setParameter( $key, $value ) {
    $this->parameters[ $key ] = $value;
    return $this;
  }

  public function setParameters( array $params ) {
    foreach ( $params as $k => $v ) {
      $this->setParameter( $k, $v );
    }
    return $this;
  }

  public function setPayload( $payload ) {
    $this->payload = $payload;
  }

  public function getCall() {
    return $this->call;
  }

  public function getParameter( $key ) {
    if ( !isset( $this->parameters[ $key ] ) ) {
      throw new Request\Exception(
        "Invalid Service Request parmater: {$key}"
      );
    }

    return $this->parameters[ $key ];
  }

  public function getPayload() {
    return $this->payload;
  }

  public function execute() {
    return $this->service->execute( $this, new Response( $this ) );
  }
}