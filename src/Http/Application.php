<?php

namespace Mduk\Gowi\Http;

use Psr\Log\LoggerAwareInterface as PsrLoggerAware;
use Psr\Log\LoggerInterface as PsrLogger;

use Mduk\Gowi\Factory;
use Mduk\Gowi\Logger\PhpErrorLog as PhpErrorLogger;

use Mduk\Dot;
use Mduk\Dot\Exception\InvalidKey as DotInvalidKeyException;

use Mduk\Gowi\Http\Application\Stage;

class Application implements PsrLoggerAware {

    protected $stages = [];
    protected $log;
    protected $services = [];
    protected $config;
    protected $request;
    protected $response;
    protected $defaultResponse;
	protected $serviceFactory;

    public function __construct() {
        $this->config = new Dot( [ 'debug' => false ] );
		$this->serviceFactory = new Factory;
    }

    public function addStage( Stage $stage ) {
        $this->stages[] = $stage;
    }

    public function run( Request $req = null, Response $res = null ) {
        $this->request = ( $req ) ?: Request::createFromGlobals();
        $this->response = ( $res ) ?: $this->getDefaultResponse();

        $this->debugLog( function( $app ) {
          $classes = implode( ', ', array_map( function( $e ) {
            return get_class( $e );
          }, $this->stages ) );

          return "Running Application with stages: {$classes}";
        } );

        return $this->execute( $this->stages );
    }

    public function setLogger( PsrLogger $log ) {
        $this->log = $log;
    }

    public function getLogger() {
        if (!$this->log) {
            $this->log = new PhpErrorLogger;
        }

        return $this->log;
    }

    public function setConfig( $key, $value ) {
        $this->debugLog( function( $app ) use ( $key, $value ) {
            $valueStr = print_r( $value, true );
            return "Application config update. {$key} => {$valueStr}";
        } );

        $this->config->set( $key, $value );
    }

    public function setConfigArray( array $array ) {
        $newConfig = array_merge( $this->config->getArray(), $array );
        $this->config = new Dot( $newConfig );
    }

    public function applyConfigArray( array $array ) {
      $this->setConfigArray( array_replace_recursive(
        $this->getConfigArray(),
        $array
      ) );
    }

    public function getConfig( $key, $default = null ) {
        try {
            return $this->config->get( $key );
        } catch ( DotInvalidKeyException $e ) {
            if ( $default === null) {
                throw new Application\Exception(
                    "Invalid config key: {$key}",
                    Application\Exception::INVALID_CONFIG_KEY
                );
            }

            return $default;
        }
    }

    public function getConfigArray() {
        return $this->config->getArray();
    }
	
	public function setServiceFactory( Factory $factory ) {
		$this->serviceFactory = $factory;
	}

    public function setService( $name, $service ) {
        if ( isset( $this->services[ $name ] ) ) {
            throw new Application\Exception(
                "Service {$name} is already registered!",
                Application\Exception::SERVICE_ALREADY_REGISTERED
            );
        }

        $this->debugLog( function( $app ) use ( $name, $service ) {
            return "Set service '{$name}' to " . get_class( $service );
        } );

        $this->services[ $name ] = $service;
    }

    public function getService( $name ) {
        if ( isset( $this->services[ $name ] ) ) {
			return $this->services[ $name ];
		}

		try {
			return $this->serviceFactory->get( $name );
		}
		catch ( \Exception $e ) {
			throw new Application\Exception(
				"Service {$name} is is not registered!",
				Application\Exception::SERVICE_NOT_REGISTERED
			);
        }
    }

    public function debugLog( \Closure $closure ) {
        $app = $this;
        if ( $this->getConfig('debug') ) {
            $this->getLogger()->debug( $closure( $app ) );
        }
    }

    protected function execute( $stages ) {
        if ( count( $stages ) == 0 ) {
            return $this->response;
        }

        $stage = array_shift( $stages );

        $this->debugLog( function( $app ) use ( $stage ) {
            return "Executing stage: " . get_class( $stage );
        } );

        $result = $stage->execute( $this, $this->request, $this->response );

        $this->debugLog( function( $app ) use ( $stage, $result ) {
            return 'Stage ' . get_class( $stage ) . ' returned: ' . var_export( $result, true );
        } );

        if ( $result instanceof Stage ) {
            array_unshift( $stages, $result );
        }

        if ( $result instanceof Response ) {
            return $result;
        }

        if ( $result instanceof self ) {
            return $result->run( $this->request, $this->response );
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


