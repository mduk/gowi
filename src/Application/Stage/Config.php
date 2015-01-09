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

    protected $fileName;
    protected $namespace;

    public function __construct( $fileName, $namespace=null ) {
        $this->fileName = $fileName;
        $this->namespace = $namespace;
    }

    public function execute( Application $app, Request $req, Response $res ) {
        $path = $this->fileName;
        $extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

        switch ( $extension ) {
            case 'php':
                $reader = new ZendConfig( require $path );
                $config = $reader->toArray();
                break;

            case 'ini':
                $reader = new IniReader;
                $config = $reader->fromFile( $path );
                break;

            case 'xml':
                $reader = new XmlReader;
                $config = $reader->fromFile( $path );
                break;

            case 'json':
                $reader = new JsonReader;
                $config = $reader->fromFile( $path );
                break;

            case 'yaml':
                $config = YamlParser::parse( $path );
                break;

            default:
                throw new Config\Exception(
                    "Unknown file type: {$path}",
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

