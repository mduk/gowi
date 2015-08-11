<?php

namespace Mduk\Gowi\Service;

class RouterTest extends \PHPUnit_Framework_TestCase {
  public function testOkRoute() {
    $service = new Router( [
      '/foo/{id}' => [
        'GET' => [
          'foo' => 'bar'
        ]
      ]
    ] );

    $result = $service->request( 'route' )
      ->setParameter( 'path', '/foo/123' )
      ->setParameter( 'method', 'GET' )
      ->execute()
      ->getResults()
      ->shift();

    $this->assertArrayHasKey( 'route', $result );
    $this->assertArrayHasKey( 'params', $result );
    $this->assertArrayHasKey( 'config', $result );

    $this->assertEquals( '/foo/{id}', $result['route'],
      "The 'route' key should have contained the pattern matched" );

    $this->assertArrayHasKey( 'id', $result['params'],
      "Route params should contain an 'id' key" );

    $this->assertEquals( 123, $result['params']['id'],
      "The 'id' parameter should contain the value extracted from the path parameter" );

    $this->assertArrayHasKey( 'foo', $result['config'],
      "Configuration supplied under the method key should be passed back as the route config." );
  }

  public function testNoRoute() {
    $service = new Router( [] );

    $result = $service->request( 'route' )
      ->setParameter( 'path', '/foo/123' )
      ->setParameter( 'method', 'GET' )
      ->execute()
      ->getResults()
      ->shift();

    $this->assertIsServiceError( 'not_found', $result );
    $this->assertArrayHasKey( 'path', $result['context'] );
    $this->assertEquals( '/foo/123', $result['context']['path'] );
  }

  public function testInvalidMethod() {
    $service = new Router( [
      '/foo/{id}' => [
        'GET' => []
      ]
    ] );

    $result = $service->request( 'route' )
      ->setParameter( 'path', '/foo/123' )
      ->setParameter( 'method', 'POST' )
      ->execute()
      ->getResults()
      ->shift();

    $this->assertIsServiceError( 'method_not_allowed', $result );
    $this->assertArrayHasKey( 'path', $result['context'] );
    $this->assertEquals( '/foo/123', $result['context']['path'] );
    $this->assertEquals( 'POST', $result['context']['method'] );
  }

  protected function assertIsServiceError( $error, $result ) {
    $this->assertArrayHasKey( 'error', $result );
    $this->assertEquals( $error, $result['error'] );
    $this->assertArrayHasKey( 'context', $result );
  }
}
