<?php

namespace Mduk\Gowi\Http\Application\Stage\Redirect;

class Exception extends \Exception {
    const LOCATION_REQUIRED = 1;
    const INVALID_STATUS_CODE = 2;
}
