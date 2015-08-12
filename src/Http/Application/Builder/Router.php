<?php

namespace Mduk\Gowi\Http\Application\Builder;

use Mduk\Application\Stage\InitRouter as InitRouterStage;
use Mduk\Application\Stage\MatchRoute as MatchRouteStage;
use Mduk\Application\Stage\SelectResponseType as SelectResponseTypeStage;
use Mduk\Application\Stage\SelectRequestTranscoder as SelectRequestTranscoderStage;
use Mduk\Application\Stage\InitResponseTranscoder as InitResponseTranscoderStage;
use Mduk\Application\Stage\DecodeRequestBody as DecodeRequestBodyStage;

use Mduk\Gowi\Http\Application\Builder as BuilderAbstract;

use Mduk\Gowi\Http\Application;

/**
 * Build a Router Application.
 *
 * The Router Application simply uses a 'router' service
 * to get information on what kind of Application should
 * handle the routed Request.
 */
class Router extends BuilderAbstract {

  protected $routes = [];

  public function defineRoute( $type, $hook, $config ) {
    $expandedHook = $this->expandHook( $hook );

    if ( $this->getDebug() ) {
      $this->getLogger()
        ->debug(
          __CLASS__ . ": Defining route: {$expandedHook['type']} " .
          "{$expandedHook['method']} {$expandedHook['path']}"
        );
    }

    $this->routes[] = [
      'type' => $type,
      'hook' => $expandedHook,
      'config' => $config
    ];
  }

  public function build( Application $app = null, array $config = [] ) {
    $app = parent::build( $app, $config );

    $app->addStage( new \Mduk\Gowi\Http\Application\Stage\InitRouter );
    $app->addStage( new \Mduk\Gowi\Http\Application\Stage\MatchRoute );

    $allRoutes = [];

    if ( $this->getLogger() && $this->routes == [] ) {
      $this->getLogger()->warning( __CLASS__ . ": No routes have been defined. This is going nowhere fast." );
    }

    foreach ( $this->routes as $routeSpec ) {
      $builder = $this->getApplicationBuilderFactory()->get( $routeSpec['type'] );
      
      if ( is_callable( [ $builder, 'buildRoutes' ] ) ) {
        $builtRoutes = $builder->buildRoutes( $routeSpec['hook'], $routeSpec['config'] );
      }
      else {
        $builtRoutes = [
          $routeSpec['hook']['path'] => [
            $routeSpec['hook']['method'] => $routeSpec['config']
          ]
        ];
      }

      $allRoutes = array_replace_recursive( $allRoutes, $builtRoutes );
    }

    $app->setConfig( 'routes', $allRoutes );

    return $app;
  }

  protected function expandHook( $hook ) {
    if ( is_string( $hook ) ) {
      if ( strpos( $hook, ':' ) !== false ) {
        $shrapnel = explode( ':', $hook );
        return [
          'method' => $shrapnel[0],
          'path' => $shrapnel[1]
        ];
      }
      else {
        return [
          'method' => 'GET',
          'path' => $hook
        ];
      }
    }
  }

}
