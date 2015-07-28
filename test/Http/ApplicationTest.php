<?php

namespace Mduk\Gowi\Http;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage;
use Mduk\Gowi\Http\Application\Stage\Stub as StubStage;

use Mduk\Gowi\Factory;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ApplicationTest extends \PHPUnit_Framework_TestCase {

    public function testRun() {
        $stage1 = $this->mockStage();
        $stage1->expects( $this->once() )->method( 'execute' );

        $stage2 = $this->mockStage();
        $stage2->expects( $this->once() )->method( 'execute' );

        $app = new Application;
        $app->addStage( $stage1 );
        $app->addStage( $stage2 );
        $app->run();
    }

    public function testRun_ReturnStage() {
        $stage1 = $this->mockStage();
        $stage1->expects( $this->once() )
            ->method( 'execute' );

        $stage2 = $this->mockStage();
        $stage2->expects( $this->once() )
            ->method( 'execute' )
            ->will( $this->returnValue( $stage1 ) );

        $app = new Application;
        $app->addStage( $stage2 );
        $app->run();
    }

    public function testRun_ReturnResponse() {
        $stage1 = $this->mockStage();
        $stage1->expects( $this->once() )
            ->method( 'execute' )
            ->will( $this->returnValue( new Response ) );

        $stage2 = $this->mockStage();
        $stage2->expects( $this->never() )
            ->method( 'execute' );

        $app = new Application;
        $app->addStage( $stage1 );
        $app->addStage( $stage2 );
        $app->run();
    }

    public function testConfig() {
        $app = new Application;
        $app->setConfig( 'foo', 'bar' );
        $this->assertEquals( 'bar', $app->getConfig( 'foo' ) );
    }

    public function testGetConfig_InvalidKey_NoDefault() {
        $this->setExpectedException( '\\Mduk\\Gowi\\Http\\Application\\Exception' );
        $app = new Application;
        $app->getConfig( 'foo' );
    }

    public function testGetConfig_InvalidKey_WithDefault() {
        $app = new Application;
        $this->assertEquals( 'bar', $app->getConfig( 'foo', 'bar' ) );
    }

    public function testApplyConfigArray() {
        $app = new Application;
        $app->setConfigArray( [
            'foo' => 'bar',
            'foo2' => [
                'bar2' => 'baz'
            ]
        ] );
        $app->applyConfigArray( [
            'bar' => 'baz',
            'foo2' => [
                'foo2' => 'bar',
                'bar2' => 'qha'
            ]
        ] );

        $this->assertEquals( 'qha', $app->getConfig( 'foo2.bar2' ),
          "foo2.bar2 should have been overwritten" );

        $this->assertEquals( 'bar', $app->getConfig( 'foo2.foo2' ),
          "foo2.foo2 should have been created" );

        $this->assertEquals( 'bar', $app->getConfig( 'foo' ),
          "foo should have remained set to \"bar\"" );
    }

    public function testServices() {
        $service = (object) [ 'foo' => 'bar' ];
        $app = new Application;
        $app->setService( 'foo', $service );

        $this->assertEquals( $service, $app->getService( 'foo' ),
            "Didn't get the expected service back" );
    }

	public function testServiceFactory() {
		$factory = new Factory( [
			'foo' => function() {
				return 'bar';
			}
		] );

		$app = new Application;
		$app->setServiceFactory( $factory );

		$this->assertEquals( 'bar', $app->getService( 'foo' ),
			"Getting the 'foo' service should have returned 'bar'" );
	}

	public function testLog() {
		$app = new Application;

		$this->assertInstanceOf( '\\Psr\\Log\\LoggerInterface', $app->getLogger(),
			"getLogger should return a PsrLogger");

		$log = new ArrayLogger;
		$app->setLogger( $log );

		$this->assertInstanceOf( '\\Psr\\Log\\LoggerInterface', $app->getLogger(),
			"getLogger should return a Psr LoggerInterface");
	}

	public function testDebugLogging_DebugOff() {
		$this->assertDebugLog( false, 0,
			"Debug off should yeild no debug log messages" );
	}

	public function testDebugLogging_DebugOn() {
		$this->assertDebugLog( true, 4,
			"Debug on should yeild 3 debug log messages" );
	}

	public function assertDebugLog( $debug, $msgCount, $message ) {
		$log = new ArrayLogger;
		$app = new Application;
		$app->setConfig( 'debug', $debug );
		$app->setLogger( $log );

		$app->addStage( new StubStage( function( $app, $req, $res ) {
			$app->setService( 'foo', new \stdClass );
			return $res;
		} ) );

		$app->run();

		$this->assertCount( $msgCount, $log->messages, $message );
	}

    protected function mockStage() {
        return $this->getMock( '\\Mduk\\Gowi\\Http\\Application\\Stage' );
    }
}

class ArrayLogger extends \Psr\Log\AbstractLogger {

	public $messages = [];

	public function log( $level, $message, array $context=[] ) {
		$this->messages[] = [
			'level' => $level,
			'message' => $message,
			'context' => $context
		];
	}

}

