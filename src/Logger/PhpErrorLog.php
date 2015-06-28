<?php

namespace Mduk\Gowi\Logger;

use Psr\Log\AbstractLogger as PsrLogger;

class PhpErrorLog extends PsrLogger {
  public function log( $level, $message, array $context = [] ) {
    error_log(
      strtoupper( $level ) .
      ': ' . $message .
      ( $context != [] )
        ? "\nContext: " . print_r( $context, true )
        : ''
    );
  }
}

