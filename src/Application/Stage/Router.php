<?php

namespace Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Application;
use Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Router implements Stage {
    protected $options;

    public function __construct( array $options=[] ) {
        $this->options = $options;
    }

    /**
     * Stage
     *
     * @param \Mduk\Gowi\Application $app The Application instance
     * @param Request $req The Request instance
     * @param Response $res The Response instance
     * @return mixed
     * @throws Router/Exception
     */
    public function execute( Application $app, Request $req, Response $res ) {
        $config = $this->getRouterConfig( $app );

        $matcher = new UrlMatcher(
            $this->buildRoutes( $config ),
            $this->buildContext( $req )
        );

        try {
            $attributes = $matcher->match( $req->getPathInfo() );
            $req->attributes->add( $attributes );
            return $this->getStage( $attributes['stage'] );
        }
        catch ( ResourceNotFoundException $e ) {
            return null;
        }
    }

    protected function getRouterConfig( Application $app ) {
        $name = $this->getOption( 'name' );
        $routerConfig = $app->getConfig( 'router' );
        if ( $name ) {
            if ( !isset( $routerConfig[ $name ] ) ) {
                throw new \Exception("No router config: {$name}");
            }
            return $routerConfig[ $name ];
        }
        else {
            return $routerConfig;
        }
    }

    protected function getOption( $option ) {
        if ( !isset( $this->options[ $option ] ) ) {
            return null;
        }

        return $this->options[ $option ];
    }

    /**
     * Build a RequestContext for the Symfony Router
     *
     * @param Request $req The current request
     * @return RequestContext
     */
    protected function buildContext( Request $req ) {
        $context = new RequestContext;
        $context->fromRequest( $req );
        return $context;
    }

    /**
     * Build a RouteCollection for the Symfony router from config
     *
     * @param array $config Route configuration
     * @return RouteCollection
     */
    protected function buildRoutes( array $config ) {
        $routes = new RouteCollection;

        foreach( $config as $name => $route ) {
            $routes->add( $name, new Route( $route['path'], $route['attributes'] ) );
        }

        return $routes;
    }

    /**
     * Instantiate and return a stage instance. If it doesn't exist, throw an exception.
     *
     * @param $stageClass
     * @throws Router\Exception
     * @return Stage An application stage instance
     */
    protected function getStage( $stageClass ) {
        if ( class_exists( $stageClass ) ) {
            $stage = new $stageClass;

            if ( $stage instanceof Stage ) {
                return $stage;
            }
        }

        throw new Router\Exception(
            'Could not resolve stage: ' . $stageClass,
            Router\Exception::CANNOT_RESOLVE_STAGE
        );
    }
}
