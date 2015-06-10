<?php

namespace Mduk\Gowi;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
  public function testGet() {
    $c = new Collection( array( 1,2,3,4,5,6,7,8,9,10 ) );

    $this->assertEquals( 5, $c->get( 4 ) );
    $this->assertEquals( array( 5 ), $c->get( 4, 1 ) );
    $this->assertEquals( array( 5, 6, 7 ), $c->get( 4, 3 ) );
  }

  public function testGet_InvalidOffset() {
    try {
      $c = new Collection;
      $c->get( 4 );
      $this->fail();
    }
    catch ( Collection\Exception $e ) {}
  }

  public function testShift() {
    $c = new Collection( array() );
    $this->assertEquals( null, $c->shift() );

    $c = new Collection( array( 1, 2 ) );

    $this->assertEquals( 2, $c->count() );
    $this->assertEquals( 1, $c->shift() );
    $this->assertEquals( 1, $c->count() );
  }

  // Test that the collection can be iterated
  public function testIteration() {
    $expected = array( 1, 2, 3 );
    $c = new Collection( $expected );
    $i = 0;
    foreach ( $c as $e ) {
      $this->assertEquals( $expected[ $i ], $e );
      $i++;
    }
  }

  // Test that the collection works as an array
  public function testArrayAccess() {
    $c = new Collection( array( 1, 2, 3 ) );
    $c[3] = 4;

    $this->assertEquals( 4, $c[3] );

    unset( $c[3] );

    $this->assertFalse( isset( $c[3] ) );
    $this->assertTrue( $c[0] == 1 );
    $this->assertTrue( isset( $c[1] ) );
    $this->assertFalse( isset( $c[4] ) );

    $c[] = 5;
    $this->assertTrue( isset( $c[4] ) );
  }

  public function testCount() {
    $c = new Collection();

    $this->assertTrue( 0 === count( $c ) );
    $this->assertTrue( 0 === $c->count() );

    $c = new Collection( array( 1, 2, 3 ) );

    $this->assertEquals( 3, count( $c ) );
    $this->assertEquals( 3, $c->count() );

    $c = new Collection( array(), 123 );
    $this->assertEquals( 123, count( $c ) );
    $this->assertEquals( 123, $c->count() );
  }
}

