<?php

namespace Mduk\Gowi\Http;

use Symfony\Component\HttpFoundation\Response as SfResponse;

class Response extends SfResponse {
		
	public function ok() {
		$this->setStatusCode( 200 );
		return $this;
	}

	public function notFound() {
		$this->setStatusCode( 404 );
		return $this;
	}

	public function error() {
		$this->setStatusCode( 500 );
		return $this;
	}

	public function text( $text ) {
		$this->contentType( 'text/plain' );
		$this->setContent( $text );
	}

	public function html( $html ) {
		$this->contentType( 'text/html' );
		$this->setContent( $html );
	}

	public function xml( $xml ) {
		$this->contentType( 'application/xml' );

		if ( $xml instanceof \DOMDocument ) {
			$xml = $xml->saveXML();
		}

		if ( $xml instanceof \SimpleXMLElement ) {
			$xml = $xml->asXML();
		}

		$this->setContent( $xml );
	}

	public function json( $data ) {
		$this->contentType( 'application/json' );
		$this->setContent( json_encode( $data ) );
	}

	protected function contentType( $mime ) {
		$this->headers->set( 'Content-Type', $mime );
	}

}

