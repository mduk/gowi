<?php

namespace Mduk\Gowi;

use Psr\Log\LoggerInterface as PsrLogger;
use Psr\Log\NullLogger as PsrNullLogger;

use Mduk\Gowi\Application\Exception;

use Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class Application {

    protected $baseDir = '';
    protected $stages = [];
    protected $log;
    protected $services = [];
    protected $config = [ 'debug' => false ];
    protected $request;
    protected $response;
    protected $defaultResponse;

    public function __construct( $baseDir ) {
        $this->baseDir = $baseDir;
    }

    public function getBaseDir() {
        return $this->baseDir;
    }

    public function addStage( Stage $stage ) {
        $this->stages[] = $stage;
    }

    public function run( Request $req = null, Response $res = null ) {
        $this->request = ( $req ) ?: Request::createFromGlobals();
        $this->response = ( $res ) ?: $this->getDefaultResponse();

        return $this->execute( $this->stages );
    }

    public function setLog( PsrLogger $log ) {
        $this->log = $log;
    }

    public function getLog() {
        if (!$this->log) {
            $this->log = new PsrNullLogger;
        }

        return $this->log;
    }

    public function setConfig( array $config ) {
        $this->config = array_merge( $this->config, $config );
    }

    public function getConfig( $key = null ) {
        if ( !$key ) {
            return $this->config;
        }

        if ( !isset( $this->config[ $key ] ) ) {
            throw new Exception(
                "Invalid config key: {$key}",
                Exception::INVALID_CONFIG_KEY
            );
        }

        return $this->config[ $key ];
    }

    public function setService( $name, $service ) {
        if ( isset( $this->services[ $name ] ) ) {
            throw new Application\Exception(
                "Service {$name} is already registered!",
                Application\Exception::SERVICE_ALREADY_REGISTERED
            );
        }

        $this->debug( function( $app ) use ( $name, $service ) {
            $app->getLog()->debug( "Set service '{$name}' to " . get_class( $service ) );
        } );

        $this->services[ $name ] = $service;
    }

    public function getService( $name ) {
        if ( !isset( $this->services[ $name ] ) ) {
            throw new Application\Exception(
                "Service {$name} is is not registered!",
                Application\Exception::SERVICE_NOT_REGISTERED
            );
        }

        return $this->services[ $name ];
    }

    protected function execute( $stages ) {
        if ( count( $stages ) == 0 ) {
            return $this->response;
        }

        $stage = array_shift( $stages );

        $this->debug( function( $app ) use ( $stage ) {
            $app->getLog()->debug( "Executing stage: " . get_class( $stage ) );
        } );

        $result = $stage->execute( $this, $this->request, $this->response );

        $this->debug( function( $app ) use ( $stage, $result ) {
            $msg = 'Stage ' . get_class( $stage ) . ' returned: ' . var_export( $result, true );
            $app->getLog()->debug( $msg );
        } );

        if ( $result instanceof Stage ) {
            array_unshift( $stages, $result );
        }

        if ( $result instanceof Response ) {
            return $result;
        }

        return $this->execute( $stages );
    }

    protected function getDefaultResponse() {
        if (!$this->defaultResponse) {
            $this->defaultResponse = new Response('Hello World!', 200, array(
                'Content-Type' => 'text/plain'
            ) );
        }

        return clone $this->defaultResponse;
    }

    protected function debug( \Closure $closure ) {
        $app = $this;
        if ( $this->getConfig('debug') ) {
            $closure( $app );
        }
    }
}


