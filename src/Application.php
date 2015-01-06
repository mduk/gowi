<?php

namespace Mduk\Gowi;

use Exception;

use Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

use Psr\Log\LoggerInterface as Log;
use Monolog\Logger;
use Monolog\Handler\NullHandler as NullLogHandler;

class Application {

    protected $stages = array();
    protected $config = array( 'debug' => false );
    protected $request;
    protected $response;
    protected $defaultResponse;
    protected $log;

    public function addStage( Stage $stage ) {
        $this->stages[] = $stage;
    }

    public function run( Request $req = null, Response $res = null ) {
        $this->request = ( $req ) ?: Request::createFromGlobals();
        $this->response = ( $res ) ?: $this->getDefaultResponse();

        return $this->execute( $this->stages );
    }

    public function setConfig( array $config ) {
        $this->config = array_merge( $this->config, $config );
    }

    public function getConfig( $key = null ) {
        if ( !$key ) {
            return $this->config;
        }

        if ( !isset( $this->config[ $key ] ) ) {
            throw new ApplicationException(
                "Invalid config key: {$key}",
                ApplicationException::INVALID_CONFIG_KEY
            );
        }

        return $this->config[ $key ];
    }

    public function setLog( Log $log ) {
        $this->log = $log;
    }

    public function getLog() {
        if (!$this->log) {
            $this->log = new Logger( 'application', array(
                new NullLogHandler
            ) );
        }

        return $this->log;
    }

    protected function execute( $stages ) {
        if ( count( $stages ) == 0 ) {
            return $this->response;
        }

        $stage = array_shift( $stages );

        $result = $stage->execute( $this, $this->request, $this->response );

        if ( $this->getConfig( 'debug' ) ) {
            $stageType = get_class( $stage );
            $returnType = ( is_object( $result ) )
                ? get_class( $result )
                : gettype( $result );

            $this->getLog()->debug(
                sprintf( 'Exectued stage: %s. Returned: %s', $stageType, $returnType )
            );
        }

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

}

class ApplicationException extends Exception {
    const INVALID_CONFIG_KEY = '1';
}

