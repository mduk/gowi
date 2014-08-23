<?php

namespace Mduk\Gowi;

use Exception;

use Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class Application {

	protected $stages = array();
	protected $config = array();
	protected $request;
	protected $response;
	protected $defaultResponse;

    public function addStage( Stage $stage ) {
        $this->stages[] = $stage;
    }

	public function run(Request $req = null, Response $res = null ) {
		$this->request = ( $req ) ?: Request::createFromGlobals();
		$this->response = ( $res ) ?: $this->getDefaultResponse();

		return $this->execute( $this->stages );
	}

	public function setConfig( array $config ) {
		$this->config = array_merge( $config, $this->config );
	}

	public function getConfig( $key = null ) {
		if ( !$key ) {
			return $this->config;
		}

		if ( !isset( $this->config[ $key ] ) ) {
			throw ApplicationException(
				"Invalid config key: {$key}",
				ApplicationException::INVALID_CONFIG_KEY
			);
		}

		return $this->config[ $key ];
	}

	protected function execute( $stages ) {
		if ( count( $stages ) == 0 ) {
			return $this->response;
		}

		$stage = array_shift( $stages );

		try {
			$result = $stage->execute( $this, $this->request, $this->response );
		}
		catch ( Exception $e ) {
			return $this->response
				->error()
				->text( (string) $e );
		}

		if ( $result instanceof Stage ) {
			array_unshift( $stages, $result );
		}

		if ( $result instanceof Response ) {
			return $result;
		}

		return $this->execute( $stages );
	}

	protected function getDefaultResponse() {
		if (!$this->defaultResponse) {
			$this->defaultResponse = new Response('Hello World!', 200, array(
				'Content-Type' => 'text/plain'
			) );
		}

		return clone $this->defaultResponse;
	}

}

class ApplicationException extends Exception {
	const INVALID_CONFIG_KEY = 'invalidConfigKey';
}

