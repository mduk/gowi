<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

/**
 * Select Request Transcoder
 *
 * If the Incoming HTTP Request contains no Request Body, then this stage has nothing to
 * do and will simply return.
 *
 * For Incoming HTTP Requests that contain a Request Body, this stage will attempt to
 * match the Incoming HTTP Request's Content-Type Header to a one of the available
 * Request Transcoders as defined in [http.request.transcoders.*]. The selected
 * Transcoder will be assigned to [http.request.transcoder].
 *
 * @getconfig http.request.transcoders.*
 * @getconfig transcoders.*
 * @setconfig http.request.content_type
 * @setconfig http.request.transcoder
 */
class SelectRequestTranscoder implements Stage {

  public function execute( Application $app, Request $req, Response $res ) {
    if ( !$req->getContent() ) {
      return;
    }

    $requestContentType = $req->headers->get( 'Content-Type' );

    try {
      $requestTranscoderName = $app->getConfig( "http.request.transcoders.{$requestContentType}" );
    }
    catch ( Application\Exception $e ) {
      return new Respond\UnsupportedMediaType;
    }

    $requestTranscoder = $app->getConfig( "transcoder.{$requestTranscoderName}" );

    $app->setConfig( 'http.request.content_type', $requestContentType );
    $app->setConfig( 'http.request.transcoder', $requestTranscoder );
  }

}
