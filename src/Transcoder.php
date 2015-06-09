<?php

namespace Mduk\Gowi;

interface Transcoder {
	public function encode( $in );
	public function decode( $in );
}

