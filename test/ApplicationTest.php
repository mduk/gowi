<?php

namespace Mduk\Gowi;

use Mduk\Gowi\Application;
use Mduk\Gowi\Application\Stage;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

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

	public function mockStage() {
		return $this->getMock( '\\Mduk\\Gowi\\Application\\Stage' );
	}
}

