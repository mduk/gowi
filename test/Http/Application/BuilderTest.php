<?php

namespace Mduk\Gowi\Http\Application;

use Mduk\Gowi\Http\Application;

class BuilderTest extends \PHPUnit_Framework_TestCase {
  public function testNoErrorsAreRaised() {
    $builder = new EmptyBuilder;
    $app = new Application;
    $builder->build( $app );
  }
}

class EmptyBuilder extends Builder {

}
