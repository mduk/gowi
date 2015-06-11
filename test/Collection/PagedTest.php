<?php

namespace Mduk\Gowi\Collection;

class PagedTest extends \PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->collection = new Paged( array(
      1,2,3,4,5,6,7,8,9,10,
      11,12,13,14,15,16,17,18,19,20,
      21,22,23,24,25,26,27,28,29,30,
      31,32,33,34,35,36,37
    ) );
  }

  // Test that the number of pages is calculated correctly
  public function testNumPages() {
    $this->assertEquals( 4, $this->collection->numPages() );
    $this->assertEquals( 2, $this->collection->numPages( 20 ) );
  }

  public function testPageZero() {
    try {
      $this->collection->page( 0 );
      $this->fail();
    }
    catch ( Paged\Exception $e ) {}
  }

  public function testPageOneThousand() {
    try {
      $this->collection->page( 1000 );
      $this->fail();
    }
    catch ( Paged\Exception $e ) {}
  }

  // Test that pages are retrieved properly
  public function testPage() {
    $pageZero = $this->collection->page( 1 );
    $this->assertEquals( array( 1,2,3,4,5,6,7,8,9,10 ), $pageZero->getAll() );

    $pageOne = $pageZero->nextPage();
    $this->assertEquals( array( 11,12,13,14,15,16,17,18,19,20 ), $pageOne->getAll() );
    
    $pageTwo = $pageOne->nextPage();
    $this->assertEquals( array( 21,22,23,24,25,26,27,28,29,30 ), $pageTwo->getAll() );

    $this->assertTrue( $pageOne === $pageTwo->previousPage(),
      "Page instances should be reused" );

    $this->assertEquals( array( 31,32,33,34,35,36,37 ), $this->collection->page( 4 )->getAll() );

    $this->assertEquals( array( 1,2,3,4,5 ), $this->collection->page( 1, 5 )->getAll() );
    $this->assertEquals( array( 6,7,8,9,10 ), $this->collection->page( 2, 5 )->getAll() );

    $this->assertEquals( array( 31,32,33,34,35,36,37 ), $this->collection->page( 2, 30 )->getAll() );
  }

  public function testCalculatePage() {
    $this->assertEquals( 1, $this->collection->calculatePage( 4 ) );
    $this->assertEquals( 3, $this->collection->calculatePage( 24 ) );
  }


}

