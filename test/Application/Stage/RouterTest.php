<?php

namespace Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Application;
use Mduk\Gowi\Application\Exception;

use \Mduk\Gowi\Http\Request;
use \Mduk\Gowi\Http\Response;

class RouterTest extends \PHPUnit_Framework_TestCase {

    public function testNoRoutes() {
        $app = new Application('/tmp');
        $app->setConfig( 'router', [] );

		$request = Request::create( 'http://localhost/foo' );
        $response = new Response;

        $routerStage = new Router;

        $this->assertNull($routerStage->execute($app, $request, $response),
			"Router should return null if no routes were defined.");
    }

    public function testNoMatches() {
        $app = new Application('/tmp');
        $app->setConfigArray( [
            'router' => [
                'nomatch' => [
                    'path' => '/nomatch',
                    'attributes' => []
                ]
            ]
        ] );

		$request = Request::create( 'http://localhost/user/foo' );
        $response = new Response;

        $routerStage = new Router;

        $this->assertNull($routerStage->execute($app, $request, $response),
			"Router should return null if no routes match.");
    }

    public function testStageNotFound() {
        $app = new Application('/tmp');
        $app->setConfigArray([
            'router' => [
                'myroute' => [
                    'path' => '/foo',
                    'attributes' => [
                        'stage' => 'Stage!'
                    ]
                ]
            ]
        ]);
        
        $req = Request::create('http://localhost/foo');
        $res = new Response;

        $routerStage = new Router;

        try {
            $routerStage->execute($app, $req, $res);
            $this->fail('The router should have thrown a CANNOT_RESOLVE_STAGE exception');
        }
        catch (\Exception $e) {
            $this->assertEquals('Mduk\\Gowi\\Application\\Stage\\Router\\Exception', get_class($e),
                "Exception should have been a Router\\Exception");

            $this->assertEquals(Router\Exception::CANNOT_RESOLVE_STAGE, $e->getCode(),
                "Exception should have been Router\\Exception::CANNOT_RESOLVE_STAGE");
        }
    }

    public function testMatch() {
		$request = Request::create('http://localhost/foo/bar');
		$response = new Response;

        $app = new Application('/tmp');
		$app->setConfigArray( [
			'router' => [
				'foo' => [
					'path' => '/foo/{bar}',
					'attributes' => [
						'stage' => 'Mduk\\Gowi\\Application\\Stage\\SomeStubStage'
					]
				]
			]
		] );

        $routerStage = new Router;
        $result = $routerStage->execute( $app, $request, $response );

        $this->assertTrue( $result instanceof \Mduk\Gowi\Application\Stage,
            "Result should have been a stage" );

		$this->assertEquals( 'Mduk\\Gowi\\Application\\Stage\\SomeStubStage', $request->attributes->get('stage'),
			"Request should now have a 'stage' attribute." );
    }

	public function testNamedRouter() {
		$app = new Application('/tmp');
		$app->setConfigArray( [
			'router' => [
				'public' => [
					'foo' => [
						'path' => '/foo/{foo}',
						'attributes' => [
							'stage' => 'Mduk\\Gowi\\Application\\Stage\\SomeStubStage'
						]
					]
				],
				'private' => [
					'bar' => [
						'path' => '/bar/{bar}',
						'attributes' => [
							'stage' => 'Mduk\\Gowi\\Application\\Stage\\AnotherStubStage'
						]
					]
				]
			]
		] );

		$app->addStage( new Router( [ 'name' => 'public' ] ) );
		$app->addStage( new Router( [ 'name' => 'private' ] ) );

		$res = $app->run( Request::create('http://localhost/foo/bar') );
		$this->assertEquals( 'stub', $res->getContent(),
			"First router should have matched /foo/bar" );
		
		$res = $app->run( Request::create('http://localhost/bar/baz') );
		$this->assertEquals( 'another stub', $res->getContent(),
			"Second router should have matched /bar/baz" );
	}
}

class SomeStubStage implements Stage {
	public function execute( Application $app, Request $req, Response $res ) {
		return $res->ok()->text('stub');
	}
}

class AnotherStubStage implements Stage {
	public function execute( Application $app, Request $req, Response $res ) {
		return $res->ok()->text('another stub');
	}
}
