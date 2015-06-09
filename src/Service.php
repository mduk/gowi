<?php

namespace Mduk\Gowi;

use Mduk\Gowi\Service\Request as ServiceRequest;
use Mduk\Gowi\Service\Response as ServiceResponse;

interface Service {
  public function request( $call );
  public function execute( ServiceRequest $q, ServiceResponse $r);
}

