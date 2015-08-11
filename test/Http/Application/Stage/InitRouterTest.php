<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class InitRouterTest extends \PHPUnit_Framework_TestCase {
  public function testExecute() {
    $app = new Application;
    $app->setConfig( 'routes', [] );

    $stage = new InitRouter;
    $result = $stage->execute( $app, new Request, new Response );

    $this->assertNull( $result,
      "Stage should pass execution" );
  }
}
