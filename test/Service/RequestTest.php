<?php

namespace Mduk\Gowi\Service;

class RequestTest extends \PHPUnit_Framework_TestCase {

  public function testGetParameters() {
    $r = new Request( new Shim('MyService'), 'my_call' );
    $r->setParameter( 'foo', 'oof' );
    $r->setParameter( 'bar', 'rab' );

    $this->assertEquals( [ 'foo' => 'oof', 'bar' => 'rab' ], $r->getParameters(),
      "getParameters should return an array of all parameters" );
  }

  public function testFluent() {
    $r = new Request( new Shim( 'MyService' ), 'my_call' );

    $this->assertEquals( $r, $r->setParameter( 'foo', 'bar' ),
      "setParameter should be fluent" );

    $this->assertEquals( $r, $r->setPayload( 'foo' ),
      "setPayload should be fluent" );
  }

  public function testRequiredParameterMissing() {
    $s = new Shim( 'MyService' );
    $s->setCall( 'my_call', [ $this, 'stubCall' ], [], 'my_call desc' );
    try {
      $r = new Request( $s, 'my_call', [ 'my_parameter' ] );
      $r->execute();
      $this->fail( "Should have thrown an exception" );
    }
    catch ( Request\Exception\RequiredParameterMissing $e ) {}
  }

  public function stubCall() {

  }

}

