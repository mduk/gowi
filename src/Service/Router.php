<?php

namespace Mduk\Gowi\Service;

use Mduk\Gowi\Service;

use Symfony\Component\Routing\Matcher\UrlMatcher as SfUrlMatcher;
use Symfony\Component\Routing\RequestContext as SfRequestContext;
use Symfony\Component\Routing\RouteCollection as SfRouteCollection;
use Symfony\Component\Routing\Route as SfRoute;
use Symfony\Component\Routing\Exception\ResourceNotFoundException as SfResourceNotFoundException;

class Router implements Service {
  protected $config;
  protected $routes;
  protected $requiredParameters = [
    'route' => [ 'path', 'method' ]
  ];

  public function __construct( $config ) {
    $this->config = $config;
    $this->initialiseRouter();
  }

  public function request( $call ) {
    return new Request( $this, $call, $this->requiredParameters[ $call ] );
  }

  public function execute( Request $request, Response $response ) {
    switch ( $request->getCall() ) {
      case 'route':
        $path = $request->getParameter( 'path' );
        $method = $request->getParameter( 'method' );
        return $this->route( $path, $method, $response );
    }
  }
  
  protected function initialiseRouter() {
    $this->routes = new SfRouteCollection();
    foreach ( $this->config as $routePattern => $routeParams ) {
      $route = new SfRoute( $routePattern );
      $this->routes->add( $routePattern, $route );
    }
  }

  protected function route( $path, $method, $response ) {
    $params = $this->routePath( $path );

    if ( $params === false ) {
      return $response->addError( 'not_found', [ 'path' => $path ] );
    }

    $route = $params['_route'];
    unset( $params['_route'] );

    $activeRoute = $this->config[ $route ];

    if ( !isset( $activeRoute[ $method ] ) ) {
      return $response->addError( 'method_not_allowed', [
        'path' => $path,
        'method' => $method
      ] );
    }

    $config = $this->config[ $route ][ $method ];

    return $response->addResult( [
      'route' => $route,
      'params' => $params,
      'config' => $config
    ] );

  }

  protected function routePath( $path ) {
    try {
      $matcher = new SfUrlMatcher(
        $this->routes,
        new SfRequestContext()
      );
      return $matcher->match( $path );
    }
    catch ( SfResourceNotFoundException $e ) {
      return false;
    }
  }
}
