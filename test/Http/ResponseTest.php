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

