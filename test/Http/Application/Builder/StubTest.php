<?php

namespace Mduk\Gowi\Http\Application\Builder;

class StubTest extends \PHPUnit_Framework_TestCase {
  public function testBuild() {
    $builder = new Stub;
    $app = $builder->build( null, [
      'content_type' => 'text/plain',
      'body' => 'hello!'
    ] );
    $response = $app->run();

    $this->assertEquals( 200, $response->getStatusCode() );
    $this->assertEquals( 'text/plain', $response->headers->get( 'Content-Type' ) );
    $this->assertEquals( 'hello!', $response->getContent() );
  }
}
