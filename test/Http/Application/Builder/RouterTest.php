<?php

namespace Mduk\Gowi\Http\Application\Builder;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage\Stub as StubStage;
use Mduk\Gowi\Http\Application\Builder as ApplicationBuilder;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Factory;

class RouterTest extends \PHPUnit_Framework_TestCase {

  public function testColonHookExpansion() {
    $builderFactory = new Factory( [
      'stub' => function() {
        return new StubBuilder;
      }
    ] );

    $builder = new Router;
    $builder->setApplicationBuilderFactory( $builderFactory );

    $builder->defineRoute( 'stub', 'GET:/foo', [
      'builder' => 'stub',
      'config' => []
    ] );

    $response = $builder->build()->run( Request::create( '/foo', 'GET' ) );
    $this->assertEquals( 200, $response->getStatusCode() );

    $response = $builder->build()->run( Request::create( '/foo', 'POST' ) );
    $this->assertEquals( 405, $response->getStatusCode() );
  }

  public function testNotFound() {
    $builder = new Router;
    $app = $builder->build();
    $response = $app->run( Request::create( '/foo' ) );

    $this->assertEquals( 404, $response->getStatusCode() );
  }

  public function testMethodNotAllowed() {
    $builderFactory = new Factory( [
      'stub' => function() {
        return new StubBuilder;
      }
    ] );

    $builder = new Router;
    $builder->setApplicationBuilderFactory( $builderFactory );

    $builder->defineRoute( 'stub', '/foo', [] );
    
    $response = $builder->build()->run( Request::create( '/foo', 'POST' ) );

    $this->assertEquals( 405, $response->getStatusCode() );
  }

  public function testOk() {
    $builderFactory = new Factory( [
      'stub' => function() {
        return new StubBuilder;
      }
    ] );

    $builder = new Router;
    $builder->setApplicationBuilderFactory( $builderFactory );

    $builder->defineRoute( 'stub', '/foo', [
      'builder' => 'stub',
      'config' => []
    ] );
    
    $response = $builder->build()->run( Request::create( '/foo' ) );

    $this->assertEquals( 200, $response->getStatusCode() );
  }

}

class StubBuilder extends ApplicationBuilder {
  public function build( Application $app = null, array $config = [] ) {
    if ( !$app ) {
      $app = parent::build( $app, $config );
    }

    $app->addStage( new StubStage( function( $app, $req, $res ) {
      return $res->ok()->text( 'ok' );
    }) );

    return $app;
  }
}
