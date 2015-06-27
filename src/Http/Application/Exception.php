<?php

namespace Mduk\Gowi\Http\Application;

use Exception as StdException;

class Exception extends StdException {
    const INVALID_CONFIG_KEY = 1;
    const SERVICE_ALREADY_REGISTERED = 2;
    const SERVICE_NOT_REGISTERED = 3;
}

