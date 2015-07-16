<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application as App;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class SelectResponseTypeStageTest extends \PHPUnit_Framework_TestCase {
  public function testNoResponseTranscoderConfig() {
    $req = new Request;
    $req->headers->set( 'Accept', 'application/foo-bar' );

    $stage = new SelectResponseType;
    $returned = $stage->execute( new App, $req, new Response ); 

    $this->assertNull( $returned,
      "In the event that there is no response transcoder config, ".
      "we assume that there will be no response body and return null." );
  }

  public function testNoTranscoderFoundInConfigForAcceptHeader() {
    $app = new App;
    $app->setConfig( 'http.response.transcoders.application/json', new \stdClass );

    $req = new Request;
    $req->headers->set( 'Accept', 'application/foo-bar' );

    $stage = new SelectResponseType;
    $returned = $stage->execute( $app, $req, new Response ); 

    $this->assertInstanceOf( '\\Mduk\\Gowi\\Http\\Application\\Stage\\Respond\\NotAcceptable', $returned,
      "If there was transcoder config, but no transcoder could be found for any of the accepted ".
      "MIME types, then it should return a Respond\\NotAcceptable Stage" );
  }

  public function testTranscoderFoundForSpecificMimeType() {
    $app = new App;
    $app->setConfig( 'http.response.transcoders.application/json', new \stdClass );

    $req = new Request;
    $req->headers->set( 'Accept', 'application/json' );

    $stage = new SelectResponseType;
    $returned = $stage->execute( $app, $req, new Response ); 

    $this->assertNull( $returned,
      "Null should have been returned to allow execution to continue" );

    $this->assertEquals( 'application/json', $app->getConfig( 'http.response.content_type' ),
      "The Application should have been configure with an http.response.content_type of application/json" );
  }
  
  public function testTranscoderFoundForWildcardMimeType() {
    $app = new App;
    $app->setConfig( 'http.response.transcoders.application/json', new \stdClass );

    $req = new Request;
    $req->headers->set( 'Accept', '*/*' );

    $stage = new SelectResponseType;
    $returned = $stage->execute( $app, $req, new Response ); 

    $this->assertNull( $returned,
      "Null should have been returned to allow execution to continue" );

    $this->assertEquals( 'application/json', $app->getConfig( 'http.response.content_type' ),
      "The Application should have been configure with an http.response.content_type of application/json" );
  }
}
