<?php

namespace Mduk\Gowi\Service;

use Mduk\Gowi\Service;

class Request {
  protected $service;
  protected $call;
  protected $requiredParameters = [];
  protected $payload;
  protected $parameters = [];

  public function __construct( Service $service, $call, array $requiredParameters = [] ) {
    $this->service = $service;
    $this->call = $call;
    $this->requiredParameters = $requiredParameters;
  }

  public function getRequiredParameters() {
    return $this->requiredParameters;
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
    return $this;
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

  public function getParameters() {
    return $this->parameters;
  }

  public function getPayload() {
    return $this->payload;
  }

  public function execute() {
    foreach ( $this->requiredParameters as $param ) {
      if ( !isset( $this->parameters[ $param ] ) ) {
        throw new Request\Exception\RequiredParameterMissing(
          "Parameter {$param} is required"
        );
      }
    }

    return $this->service->execute( $this, new Response( $this ) );
  }
}
