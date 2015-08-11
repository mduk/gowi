<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class MatchRouteTest extends \PHPUnit_Framework_TestCase {
  public function testExecute() {
    $app = new Application;
    $app->setConfig( 'routes', [] );
    $app->addStage( new InitRouter );
    $app->run();

    $stage = new MatchRoute;
    $stage->execute( $app, new Request, new Response );
  }
}
