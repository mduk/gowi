<?php

namespace Mduk\Gowi\Service;

class RequestTest extends \PHPUnit_Framework_TestCase {

  public function testGetParameters() {
    $r = new Request( new Shim, 'my_call' );
    $r->setParameter( 'foo', 'oof' );
    $r->setParameter( 'bar', 'rab' );

    $this->assertEquals( [ 'foo' => 'oof', 'bar' => 'rab' ], $r->getParameters(),
      "getParameters should return an array of all parameters" );
  }

  public function testFluent() {
    $r = new Request( new Shim, 'my_call' );

    $this->assertEquals( $r, $r->setParameter( 'foo', 'bar' ),
      "setParameter should be fluent" );

    $this->assertEquals( $r, $r->setPayload( 'foo' ),
      "setPayload should be fluent" );
  }

}

