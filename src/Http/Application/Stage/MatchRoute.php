<?php

namespace Mduk\Gowi\Http\Application\Stage;

use Mduk\Gowi\Http\Application\Stage\Respond\NotFound as NotFoundResponseStage;
use Mduk\Gowi\Http\Application\Stage\Respond\MethodNotAllowed as MethodNotAllowedResponseStage;
use Mduk\Gowi\Http\Application\Stage\Builder as BuilderStage;

use Mduk\Gowi\Service\Router\Exception as RouterException;

use Mduk\Gowi\Factory;
use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Application\Stage;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class MatchRoute implements Stage {

  public function execute( Application $app, Request $req, Response $res ) {
    $activeRoute = $app->getService( 'router' )
      ->request( 'route' )
      ->setParameter( 'path', $req->getPathInfo() )
      ->setParameter( 'method', $req->getMethod() )
      ->execute()
      ->getResults()
      ->shift();

    if ( isset( $activeRoute['error'] ) ) {
      switch ( $activeRoute['error'] ) {
        case 'not_found':
          return new NotFoundResponseStage;

        case 'method_not_allowed':
          return new MethodNotAllowedResponseStage;
      }
    }

    if ( !isset( $activeRoute['config']['builder'] ) ) {
      throw new \Exception(
        "Route config doesn't contain a builder name.\n" .
        print_r( $activeRoute['config'], true )
      );
    }

    $builder = $activeRoute['config']['builder'];

    if ( !isset( $activeRoute['config']['config'] ) ) {
      throw new \Exception( "Route config doesn't contain any builder config" );
    }

    $builderConfig = array_replace_recursive(
      $activeRoute['config']['config'], // Route config then Builder config
      [
        'route' => [
          'pattern' => $activeRoute['route'],
          'parameters' => $activeRoute['params'],
        ]
      ]
    );

    return new BuilderStage( $builder, $builderConfig );
  }

}
