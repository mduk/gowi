<?php

namespace Mduk\Gowi\Http\Application;

use Mduk\Gowi\Http\Application;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

interface Stage {
	public function execute( Application $app, Request $req, Response $res );
}

