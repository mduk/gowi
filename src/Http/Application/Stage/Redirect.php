<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

use Mduk\Gowi\Http\Application\Stage;

class Redirect implements Stage {
    public function execute( Application $app, Request $req, Response $res ) {
        $location = $this->getLocation( $req );
        $content = <<<HTML
<html>
    <head>
        <title>Redirecting to: $location</title>
        <meta http-equiv="refresh" content="0; url=$location">
    </head>
    <body>
        <p>Redirecting you to <a href="$location">$location</a></p>
    </body>
</html>
HTML;

        $res->setStatusCode( $this->getCode( $req ) );
        $res->headers->set( 'Location', $location );
        $res->setContent( $content );
        return $res;
    }

    protected function getLocation( Request $req ) {
        $location = $req->attributes->get( 'redirect_location' );

        if (!$location) {
            throw new Redirect\Exception(
                "No location provided in request attributes",
                Redirect\Exception::LOCATION_REQUIRED
            );
        }

        return $location;
    }

    protected function getCode( Request $req ) {
        $code = $req->attributes->get( 'redirect_code', 307 );

        if ( !in_array( $code, [ 301, 302, 303, 307 ] ) ) {
            throw new Redirect\Exception(
                "Status code {$code} isn't a redirect.",
                Redirect\Exception::INVALID_STATUS_CODE
            );
        }

        return $code;
    }
}
