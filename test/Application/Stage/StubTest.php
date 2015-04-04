<?php

namespace Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Application;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;


class StubTest extends \PHPUnit_Framework_TestCase {

    public function testExecute() {
        $stage = new Stub( function( $app, $req, $res ) {
            $res->setStatusCode( 200 );
            return $res;
        } );

        $app = new Application('/tmp');
        $req = Request::create( 'http://localhost/foo/bar' );
        $res = new Response;
        $res->setStatusCode( 500 );

        $res = $stage->execute( $app, $req, $res );
        $this->assertEquals( 200, $res->getStatusCode() );
    }

}

