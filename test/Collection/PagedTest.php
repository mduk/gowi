<?php

namespace Mduk\Gowi\Collection;

class PagedTest extends \PHPUnit_Framework_TestCase {
  // Test that the number of pages is calculated correctly
  public function testNumPages() {
    $c = new Paged( array(
      1,2,3,4,5,6,7,8,9,10,
      11,12,13,14,15,16,17,18,19,20,
      21,22,23,24,25,26,27,28,29,30,
      31,32,33,34,35,36,37
    ) );

    $this->assertEquals( 4, $c->numPages() );
    $this->assertEquals( 2, $c->numPages( 20 ) );
  }

  // Test that pages are retrieved properly
  public function testPage() {
    $c = new Paged( array(
      1,2,3,4,5,6,7,8,9,10,
      11,12,13,14,15,16,17,18,19,20,
      21,22,23,24,25,26,27,28,29,30,
      31,32,33,34,35,36,37
    ) );

    $pageZero = $c->page( 0 );
    $this->assertEquals( array( 1,2,3,4,5,6,7,8,9,10 ), $pageZero->getAll() );

    $pageOne = $pageZero->nextPage();
    $this->assertEquals( array( 11,12,13,14,15,16,17,18,19,20 ), $pageOne->getAll() );
    $this->assertEquals( array( 21,22,23,24,25,26,27,28,29,30 ), $c->page( 2 )->getAll() );
    $this->assertEquals( array( 31,32,33,34,35,36,37 ), $c->page( 3 )->getAll() );

    $this->assertEquals( array( 1,2,3,4,5 ), $c->page( 0, 5 )->getAll() );
    $this->assertEquals( array( 6,7,8,9,10 ), $c->page( 1, 5 )->getAll() );

    $this->assertEquals( array( 31,32,33,34,35,36,37 ), $c->page( 1, 30 )->getAll() );
  }

  public function testCalculatePage() {
    $c = new Paged( array(
      1,2,3,4,5,6,7,8,9,10,
      11,12,13,14,15,16,17,18,19,20,
      21,22,23,24,25,26,27,28,29,30,
      31,32,33,34,35,36,37
    ) );

    $this->assertEquals( 1, $c->calculatePage( 4 ) );
    $this->assertEquals( 3, $c->calculatePage( 24 ) );
  }


}

