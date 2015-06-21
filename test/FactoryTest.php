<?php

namespace Mduk\Gowi;

class FactoryTest extends \PHPUnit_Framework_TestCase {
  public function testGet() {
    $factory = new Factory( [
      'foo' => function() {
        return 'bar!';
      }
    ] );

    $this->assertEquals( 'bar!', $factory->get( 'foo' ),
      "get('foo') should have returned 'bar!'" );
  }

  public function testArrayAccess() {
    $factory = new Factory( [
      'foo' => function() {
        return 'bar!';
      }
    ] );

    $this->assertEquals( 'bar!', $factory['foo'],
      "get('foo') should have returned 'bar!'" );
  }
}

