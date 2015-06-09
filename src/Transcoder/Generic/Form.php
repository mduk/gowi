<?php

namespace Mduk\Gowi\Transcoder\Generic;

use Mduk\Gowi\Transcoder;

class Form implements Transcoder {

	public function encode( $in ) {
		return json_encode( $in );
	}

	public function decode( $in ) {
    $arr = [];
		parse_str( $in, $arr );
    return $arr;
	}

}
