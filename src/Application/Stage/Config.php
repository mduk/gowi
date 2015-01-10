<?php

namespace Mduk\Gowi\Application\Stage;

use Exception;

use Mduk\Gowi\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

use Mduk\Gowi\Application\Stage;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Reader\Ini as IniReader;
use Zend\Config\Reader\Xml as XmlReader;
use Zend\Config\Reader\Json as JsonReader;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Config implements Stage {

    protected $path;
    protected $namespace;
    protected $required;

    public function __construct( $path, array $options=[] ) {
        $this->path = $path;
        $this->namespace = isset( $options['namespace'] ) ? $options['namespace'] : null;
        $this->required = isset( $options['required'] ) ? $options['required'] : true;
    }

    public function execute( Application $app, Request $req, Response $res ) {
        if ( !$this->required && !is_readable( $this->path ) ) {
            return null;
        }

        if ( $this->required && !is_readable( $this->path ) ) {
            throw new Config\Exception(
                "The config file {$this->path} is unreadable",
                Config\Exception::FILE_UNREADABLE
            );
        }

        $extension = strtolower( pathinfo( $this->path, PATHINFO_EXTENSION ) );

        switch ( $extension ) {
            case 'php':
                $reader = new ZendConfig( require $this->path );
                $config = $reader->toArray();
                break;

            case 'ini':
                $reader = new IniReader;
                $config = $reader->fromFile( $this->path );
                break;

            case 'xml':
                $reader = new XmlReader;
                $config = $reader->fromFile( $this->path );
                break;

            case 'json':
                $reader = new JsonReader;
                $config = $reader->fromFile( $this->path );
                break;

            case 'yaml':
                $config = YamlParser::parse( $this->path );
                break;

            default:
                throw new Config\Exception(
                    "Unknown file type: {$this->path}",
                    Config\Exception::UNKNOWN_TYPE
                );
        }

        if ( $this->namespace ) {
            $app->setConfig( [ $this->namespace => $config ] );
        }
        else {
            $app->setConfig( $config );
        }
    }
}

