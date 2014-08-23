<?php

namespace Mduk\Gowi\Http;

class ResoonseTest extends \PHPUnit_Framework_TestCase {

	public function testOk() {
		$res = new Response;
		$res->ok();

		$this->assertEquals( 200, $res->getStatusCode() );
	}

	public function testNotFound() {
		$res = new Response;
		$res->notFound();

		$this->assertEquals( 404, $res->getStatusCode() );
	}

	public function testError() {
		$res = new Response;
		$res->error();

		$this->assertEquals( 500, $res->getStatusCode() );
	}

	public function testText() {
		$res = new Response;
		$res->text('foo');

		$this->assertMime( $res, 'text/plain' );
	}

	public function testHtml() {
		$res = new Response;
		$res->html('foo');

		$this->assertMime( $res, 'text/html' );
	}

	public function testXml() {
		$res = new Response;
		$res->xml('foo');

		$this->assertMime( $res, 'application/xml' );
	}

	public function testXml_DOMDocument() {
		$dom = new \DOMDocument( '1.0' );

		$foo = $dom->createElement( 'root' );
		$bar = $dom->createElement( 'foo' );
		$bar->setAttribute( 'bar', 'baz' );
		$foo->appendChild( $bar );
		$dom->appendChild( $foo );

		$res = new Response;
		$res->xml( $dom );

		$this->assertMime( $res, 'application/xml' );
		$this->assertEquals( "<?xml version=\"1.0\"?>\n<root><foo bar=\"baz\"/></root>\n", $res->getContent() );
	}

	public function testJson() {
		$res = new Response;
		$res->json( array( 'foo' => 'bar' ) );

		$this->assertMime( $res, 'application/json' );
		$this->assertEquals( '{"foo":"bar"}', $res->getContent() );
	}

	protected function assertMime( $res, $mime ) {
		$this->assertEquals( $mime, $res->headers->get('Content-Type') );
	}
		
}

