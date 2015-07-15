<?php

namespace Mduk\Gowi\Http\Application\Stage\Config;

use Mduk\Gowi\Http\Application\Stage\Config;
use Mduk\Gowi\Http\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;

class FileTest extends \PHPUnit_Framework_TestCase {

    public function testPhp() {
        $file = <<<EOF
<?php

return array( 
    'foo' => array(
        'bar' => 'baz'
    )
);

EOF;

        $this->assertAppIsConfigured( $file, 'php' );
    }

    public function testIni() {
        $file = <<<EOF
[foo]
bar=baz
EOF;

        $this->assertAppIsConfigured( $file, 'ini' );
    }

    public function testXml() {
        $file = <<<EOF
<?xml version="1.0" encoding="UTF-8" ?>
<config>
    <foo bar="baz"/>
</config>
EOF;

        $this->assertAppIsConfigured( $file, 'xml' );

        $file = <<<EOF
<?xml version="1.0" encoding="UTF-8" ?>
<config>
    <foo>
        <bar>baz</bar>
    </foo>
</config>
EOF;

        $this->assertAppIsConfigured( $file, 'xml' );
    }

    public function testJson() {
        $file = "{ \"foo\": { \"bar\": \"baz\" } }";
        $this->assertAppIsConfigured( $file, 'json' );
    }

    public function testYml() {
        $file = "---\nfoo:\n  bar: baz";
        $this->assertAppIsConfigured( $file, 'yml' );
    }

    public function testYaml() {
        $file = "---\nfoo:\n  bar: baz";
        $this->assertAppIsConfigured( $file, 'yaml' );
    }

    public function testRequiredUnreadable() {
        try {
            $config = new File( '/tmp/file.json', [ 'required' => true ] );
            $config->execute( new Application, new Request, new Response );
        }
        catch ( \Exception $e ) {
			$this->assertInstanceOf( 'Mduk\\Gowi\\Http\\Application\\Stage\\Config\\File\\Exception', $e,
				"Stage should have thrown an Application\\Stage\\Config\\File\\Exception." );
            $this->assertEquals( File\Exception::FILE_UNREADABLE, $e->getCode(),
                "Exception code should have been File\\Exception::FILE_UNREADABLE" );
        }
    }

    public function testInvalidExtension() {
        file_put_contents( '/tmp/file.ext', 'nonsense' );
        try {
            $config = new File( '/tmp/file.ext' );
            $config->execute( new Application, new Request, new Response );
        }
        catch ( File\Exception $e ) {
            $this->assertEquals( File\Exception::UNKNOWN_TYPE, $e->getCode(),
                "Wrong exception code" );
        }
        unlink( '/tmp/file.ext' );
    }

    public function testNamespace() {
        $file = "{ \"foo\": { \"bar\": \"baz\" } }";
        $filename = "/tmp/config.json";
        file_put_contents( $filename, $file );

        $app = new Application;
        $app->addStage( new File( $filename, [ 'namespace' => 'ns' ] ) );
        $app->run();

        $config = array(
            'ns' => array(
                'foo' => array(
                    'bar' => 'baz'
                )
            ),
            'debug' => false
        );

        $this->assertEquals( $config, $app->getConfigArray() );

        unlink( $filename );
    }

    public function testMissingOptionalFile() {
        $config = new File( '/tmp/foo.xml', [ 'required' => false ] );
        $this->assertNull( $config->execute( new Application, new Request, new Response ) );
    }

    protected function assertAppIsConfigured( $file, $ext ) {
        $filename = "/tmp/config.{$ext}";
        file_put_contents( $filename, $file );

        $app = new Application;
        $app->addStage( new File( $filename ) );
        $app->run();

        $config = array(
            'foo' => array(
                'bar' => 'baz'
            ),
            'debug' => false
        );

        $this->assertEquals( $config, $app->getConfigArray() );

        unlink( $filename );
    }

}

