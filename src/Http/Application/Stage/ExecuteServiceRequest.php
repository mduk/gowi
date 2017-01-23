<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Stage\Response\NotFound as NotFoundResponseStage;
use Mduk\Stage\Response\InternalServerError as InternalServerErrorResponseStage;

use Mduk\Gowi\Http\Application\Stage;
use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

/**
 * Execute Service Request
 *
 * Execute the Prepared Service Request at [service.request] and assign the
 * Service Response Result to [serivce.result], taking into account the
 * [service.multiplicity] key.
 *
 * The [service.multiplicity] key defines our expectations for the Service Response
 * Result and can have one of three values:
 *
 * None - We are not expecting a Service Response Result at all. An example of 
 *        this might be a call that deletes an object. If a Multiplicity of None
 *        is specified 
 *
 *  One - We are expecting one and only one result. Getting more than one result
 *        back in the Service Response constitutes an error. An example of this
 *        might be a 'getObjectById' query call, if there is more than one object
 *        with that ID, then there has been an error somewhere.
 *
 * Many - No expectations are made on the length of the Service Response Result,
 *        there could be one result, there could be twenty, there could be none.
 *        No wrong answers.
 *
 * @getconfig service.request
 * @getconfig service.multiplicity
 * @setconfig service.result
 */
class ExecuteServiceRequest implements Stage {
  public function execute( Application $app, Request $req, Response $res ) {
    $multiplicity = $app->getConfig( 'service.multiplicity', 'many' );

    $result = $app->getConfig( 'service.request' )
      ->execute()
      ->getResults();

    switch ( $multiplicity ) {
      case 'none':
        if ( $result->shift() !== null ) {
          return new InternalServerErrorResponseStage( 'Multiplicity mismatch.' );
        }
        break;

      case 'one':
        $result = $result->shift();

        if ( $result === null ) {
          return new NotFoundResponseStage;
        }

        $app->setConfig( 'service.result', $result );
        break;

      case 'many':
        $app->setConfig( 'service.result', $result );
        break;

      case 'none':
        break;

      default:
          return new InternalServerErrorResponseStage( "Unknown multiplicity: {$multiplicity}" );
    }

  }
}
