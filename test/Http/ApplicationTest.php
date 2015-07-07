<?php

namespace Mduk\Gowi\Http;

use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage;
use Mduk\Gowi\Http\Application\Stage\Stub as StubStage;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ApplicationTest extends \PHPUnit_Framework_TestCase {

    public function testRun() {
        $stage1 = $this->mockStage();
        $stage1->expects( $this->once() )->method( 'execute' );

        $stage2 = $this->mockStage();
        $stage2->expects( $this->once() )->method( 'execute' );

        $app = new Application('/tmp');
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

        $app = new Application('/tmp');
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

        $app = new Application('/tmp');
        $app->addStage( $stage1 );
        $app->addStage( $stage2 );
        $app->run();
    }

    public function testConfig() {
        $app = new Application('/tmp');
        $app->setConfig( 'foo', 'bar' );
        $this->assertEquals( 'bar', $app->getConfig( 'foo' ) );
    }

    public function testGetConfig_InvalidKey_NoDefault() {
        $this->setExpectedException( '\\Mduk\\Gowi\\Http\\Application\\Exception' );
        $app = new Application('/tmp');
        $app->getConfig( 'foo' );
    }

    public function testGetConfig_InvalidKey_WithDefault() {
        $app = new Application('/tmp');
        $this->assertEquals( 'bar', $app->getConfig( 'foo', 'bar' ) );
    }

    public function testServices() {
        $service = (object) [ 'foo' => 'bar' ];
        $app = new Application('/tmp');
        $app->setService( 'foo', $service );

        $this->assertEquals( $service, $app->getService( 'foo' ),
            "Didn't get the expected service back" );
    }

	public function testLog() {
		$app = new Application('/tmp');

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
		$app = new Application('/tmp');
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

