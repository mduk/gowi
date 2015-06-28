<?php

namespace Mduk\Gowi\Logger;

use Psr\Log\AbstractLogger as PsrLogger;

class PhpErrorLog extends PsrLogger {
  public function log( $level, $message, array $context = [] ) {
    $msg = strtoupper( $level ) . ': ' . $message;
    error_log( $msg );
  }
}

