<?php

namespace Mduk\Gowi\Transcoder\Generic;

use Mduk\Gowi\Transcoder;

class Text implements Transcoder {

	public function encode( $in ) {
    return print_r( $in, true );
  }

	public function decode( $in ) {
    throw new \Exception( "Text Transcoder is encode-only" );
	}

}
