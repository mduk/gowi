<?php

namespace Mduk\Gowi;

use Mduk\Gowi\Application;
use Mduk\Gowi\Application\Stage;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

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
        $app->setConfig( array( 'foo' => 'bar' ) );
        $this->assertEquals( 'bar', $app->getConfig( 'foo' ) );
    }

    public function testGetConfig_InvalidKey() {
        $this->setExpectedException( '\\Mduk\\Gowi\\Application\\Exception' );
        $app = new Application('/tmp');
        $app->getConfig( 'foo' );
    }

    public function testServices() {
        $service = (object) [ 'foo' => 'bar' ];
        $app = new Application('/tmp');
        $app->setService( 'foo', $service );

        $this->assertEquals( $service, $app->getService( 'foo' ),
            "Didn't get the expected service back" );
    }

    protected function mockStage() {
        return $this->getMock( '\\Mduk\\Gowi\\Application\\Stage' );
    }
}

