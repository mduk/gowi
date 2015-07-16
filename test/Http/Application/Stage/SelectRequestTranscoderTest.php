<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application as App;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class SelectRequestTranscoderTest extends \PHPUnit_Framework_TestCase {
  public function testNoResponseTranscoderConfig() {
    $stage = new SelectRequestTranscoder;
    $returned = $stage->execute( new App, new Request, new Response ); 

    $this->assertNull( $returned,
      "If there's no Request body then there's nothing to transcode, should have returned null." );
  }

  public function testUnsupportedMediaType() {
    $req = new Request( [], [], [], [], [], [], 'foobar' );
    $req->headers->set( 'Content-Type', 'application/foo-bar' );

    $stage = new SelectRequestTranscoder;
    $returned = $stage->execute( new App, $req, new Response );

    $this->assertInstanceOf( '\\Mduk\\Gowi\\Http\\Application\\Stage\\Respond\\UnsupportedMediaType', $returned,
      "If we don't have a transcoder for the request content type, then a Respond\\UnsupportedMediaType ".
      "stage should have been returned." );
  }

  public function testSupportedMediaType() {
    $app = new App;
    $app->setConfig( 'transcoder.generic:json', new \stdClass );
    $app->setConfig( 'http.request.transcoders.application/json', 'generic:json' );

    $req = new Request( [], [], [], [], [], [], json_encode( [ 'foo' => 'bar' ] ) );
    $req->headers->set( 'Content-Type', 'application/json' );

    $stage = new SelectRequestTranscoder;
    $returned = $stage->execute( $app, $req, new Response );

    $this->assertNull( $returned,
      "If we have a transcoder for the Request Content Type, then null should " .
      "have been returned to allow further execution" );

    $this->assertEquals( 'application/json', $app->getConfig( 'http.request.content_type' ),
      "Application should have been configured with a http.request.content_type of application/json" );
  }
}
