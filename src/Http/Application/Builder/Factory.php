<?php

namespace Mduk\Gowi\Http\Application\Builder;

use Mduk\Gowi\Factory as BaseFactory;
use Psr\Log\LoggerInterface as Logger;

class Factory extends BaseFactory {

  protected $debug;
  protected $transcoderFactory;
  protected $serviceFactory;
  protected $logger;

  public function get( $builder ) {
    switch ( $builder ) {

      case 'router':
        $builder = new \Mduk\Gowi\Http\Application\Builder\Router;
        break;

      case 'stub':
        $builder = new \Mduk\Gowi\Http\Application\Builder\Stub;
        break;

      default:
        throw new \Exception("Unknown application type: {$builder}" );
    }

    $builder->setDebug( $this->debug );
    $builder->setLogger( $this->logger );
    $builder->setApplicationBuilderFactory( $this );
    $builder->setTranscoderFactory( $this->transcoderFactory );
    if ( $this->serviceFactory ) {
      $builder->setServiceFactory( $this->serviceFactory );
    }
    return $builder;
  }

  public function setDebug( $debug ) {
    $this->debug = $debug;
  }

  public function setServiceFactory( BaseFactory $factory ) {
    $this->serviceFactory = $factory;
  }

  public function setTranscoderFactory( BaseFactory $factory ) {
    $this->transcoderFactory = $factory;
  }

  public function setLogger( Logger $logger ) {
    $this->logger = $logger;
  }
}
