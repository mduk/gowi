<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class Respond implements Stage {

  public function execute( Application $app, Request $req, Response $res ) {
    $res->setContent( $app->getConfig( 'http.response.body' ) );
    $res->headers->set( 'Content-Type', $app->getConfig( 'http.response.content_type' ) );
    return $res;
  }

}
