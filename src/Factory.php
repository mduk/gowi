<?php

namespace Mduk\Gowi;

use \ArrayAccess;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

class Factory implements LoggerAwareInterface,
                         ArrayAccess {

  protected $factories = [];
  protected $logger;

  public function __construct( $factories = [], LoggerInterface $logger = null ) {
    $this->factories = $factories;
    $this->logger = $logger;
  }

  public function get( $type ) {
    if ( !isset( $this->factories[ $type ] ) ) {
      throw new \Exception( "No factory for type: {$type}" );
    }

    $factory = $this->factories[ $type ];

    $object = $factory();

    if ( $object instanceof LoggerAwareInterface ) {
      $object->setLogger( $this->logger );
    }

    return $object;
  }

  public function has( $factory ) {
    return isset( $this->factories[ $factory ] );
  }

  public function setFactory( $type, \Closure $factory ) {
    $this->factories[ $type ] = $factory;
  }

  public function setLogger( LoggerInterface $logger ) {
    $this->logger = $logger;
  }

  public function offsetExists( $factory ) {
    return $this->has( $factory );
  }

  public function offsetGet( $factory ) {
    return $this->get( $factory );
  }

  public function offsetSet( $name, $factory ) {
  	$this->factories[ $name ] = $factory;
  }

  public function offsetUnset( $factory ) {
    unset( $this->factories[ $factory ] );
  }

}

