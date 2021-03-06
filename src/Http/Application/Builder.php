<?php

namespace Mduk\Gowi\Http\Application;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Factory;
use Psr\Log\LoggerInterface as Logger;

abstract class Builder {

  private $debug;
  private $transcoderFactory;
  private $serviceFactory;
  private $logger;
  private $applicationBuilderFactory;
  private $appConfig = [];

  public function setDebug( $debug ) {
    $this->debug = $debug;
  }

  public function setTranscoderFactory( Factory $factory ) {
    $this->transcoderFactory = $factory;
  }

  public function setServiceFactory( Factory $factory ) {
    $this->serviceFactory = $factory;
  }

  public function setLogger( Logger $logger ) {
    $this->logger = $logger;
  }

  public function setApplicationBuilderFactory( Factory $factory ) {
    $this->applicationBuilderFactory = $factory;
  }

  public function build( Application $app = null, array $config = [] ) {
    if ( !$app ) {
      $app = new Application;
    }

    $app->applyConfigArray( $this->appConfig );

    $app->setConfig( 'debug', $this->debug );
    $app->setConfig( 'application.builder', $this->applicationBuilderFactory );
    $app->setConfig( 'transcoder', $this->transcoderFactory );

    if ( $this->logger ) {
      $app->setLogger( $this->logger );
    }

    if ( $this->serviceFactory ) {
      $app->setServiceFactory( $this->serviceFactory );
    }

    return $app;
  }

  protected function getDebug() {
    return $this->debug;
  }

  protected function getTranscoderFactory() {
    $this->transcoderFactory;
  }

  protected function getLogger() {
    return $this->logger;
  }

  protected function getApplicationBuilderFactory() {
    if ( !$this->applicationBuilderFactory ) {
      throw new \Exception( "No Application Builder Factory has been set" );
    }

    return $this->applicationBuilderFactory;
  }

}
