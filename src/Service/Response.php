<?php

namespace Mduk\Gowi\Service;

use Mduk\Gowi\Collection;

class Response {

  protected $query;
  protected $results;

  public function __construct( Request $q ) {
    $this->query = $q;
    $this->results = new Collection;
  }

  public function setResults( Collection $c ) {
    $this->results = $c;
    return $this;
  }

  public function addResult( $o ) {
    $this->results[] = $o;
    return $this;
  }

  public function addError( $error, $context ) {
    $this->results[] = [
      'error' => $error,
      'context' => $context
    ];
    return $this;
  }

  public function getResults() {
    return $this->results;
  }

  public function getRequest() {
    return $this->query;
  }
}

