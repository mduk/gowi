<?php

namespace Mduk\Gowi\Transcoder\Generic;

use Mduk\Gowi\Transcoder;

class Json implements Transcoder {

	public function encode( $in ) {
		return json_encode( $in );
	}

	public function decode( $in ) {
		return json_decode( $in );
	}

}
