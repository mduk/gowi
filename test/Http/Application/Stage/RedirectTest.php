<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Request;

class RedirectTest extends \PHPUnit_Framework_TestCase {
    protected $app;

    public function setUp() {
        $this->app = new Application;
        $this->app->setConfigArray( [
            'router' => [
                'one' => [
                    'path' => '/one',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                        'redirect_location' => 'http://one.example.com/'
                    ]
                ],
                'two' => [
                    'path' => '/two',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                        'redirect_location' => 'http://two.example.com/',
                        'redirect_code' => 301
                    ]
                ],
                'three' => [
                    'path' => '/three',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                        'redirect_location' => 'http://three.example.com/',
                        'redirect_code' => 302
                    ]
                ],
                'four' => [
                    'path' => '/four',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                        'redirect_location' => 'http://four.example.com/',
                        'redirect_code' => 303
                    ]
                ],
                'five' => [
                    'path' => '/five',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                        'redirect_location' => 'http://five.example.com/',
                        'redirect_code' => 307
                    ]
                ],
                'six' => [
                    'path' => '/six',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                        'redirect_location' => 'http://five.example.com/',
                        'redirect_code' => 200
                    ]
                ],
                'seven' => [
                    'path' => '/seven',
                    'attributes' => [
                        'stage' => '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect',
                    ]
                ]
            ]
        ] );
        $this->app->addStage( new Router );
    }

    public function testDefaultRedirectCode() {
        $this->assertRedirect( '/one', 307, 'http://one.example.com/' );
    }

    public function test301Redirect() {
        $this->assertRedirect( '/two', 301, 'http://two.example.com/' );
    }

    public function test302Redirect() {
        $this->assertRedirect( '/three', 302, 'http://three.example.com/' );
    }

    public function test303Redirect() {
        $this->assertRedirect( '/four', 303, 'http://four.example.com/' );
    }

    public function test307Redirect() {
        $this->assertRedirect( '/five', 307, 'http://five.example.com/' );
    }

    public function testInvalidCode() {
        try {
            $this->app->run( Request::create( '/six' ) );
            $this->fail( "App should have thrown an exception." );
        }
        catch ( \Exception $e ) {
            $this->assertInstanceOf( '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect\\Exception', $e,
                "Should have been a Stage\\Redirect\\Exception" );

            $this->assertEquals( Redirect\Exception::INVALID_STATUS_CODE, $e->getCode(),
                "Should have been an INVALID_STATUS_CODE" );
        }
    }

    public function testNoLocation() {
        try {
            $this->app->run( Request::create( '/seven' ) );
            $this->fail( "App should have thrown an exception." );
        }
        catch ( \Exception $e ) {
            $this->assertInstanceOf( '\\Mduk\\Gowi\\Http\\Application\\Stage\\Redirect\\Exception', $e,
                "Should have been a Stage\\Redirect\\Exception" );

            $this->assertEquals( Redirect\Exception::LOCATION_REQUIRED, $e->getCode(),
                "Should have been an LOCATION_REQUIRED" );
        }
    }

    protected function assertRedirect( $path, $code, $location ) {
        $res = $this->app->run( Request::create( $path ) );

        $resCode = $res->getStatusCode();
        $resLocation = $res->headers->get( 'location' );

        $this->assertEquals( $code, $resCode,
            "Supposed to return code {$code}, actually returned {$resCode}" );

        $this->assertEquals( $location, $resLocation,
            "Supposed to return location {$location}, actually returned {$resLocation}" );
    }
}
