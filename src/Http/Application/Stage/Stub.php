<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Closure;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage;

class Stub implements Stage {
    public function __construct( Closure $closure ) {
        $this->closure = $closure;
    }

    public function execute( Application $app, Request $req, Response $res ) {
        $closure = $this->closure;
        return $closure( $app, $req, $res );
    }
}

