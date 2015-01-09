<?php

namespace Mduk\Gowi\Application\Stage;

use Mduk\Gowi\Application;
use Mduk\Gowi\Http\Request;
use Mduk\Gowi\Http\Response;
use Mduk\Gowi\Application\Stage\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase {

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

    public function testYaml() {
        $file = "---\nfoo:\n  bar: baz";
        $this->assertAppIsConfigured( $file, 'yaml' );
    }

    public function testInvalid() {
        $this->setExpectedException( '\\Mduk\\Gowi\\Application\\Stage\\Config\\Exception' );
        $config = new Config( 'file.ext' );
        $config->execute( new Application, new Request, new Response );
    }

    public function testNamespace() {
        $file = "{ \"foo\": { \"bar\": \"baz\" } }";
        $filename = "/tmp/config.json";
        file_put_contents( $filename, $file );

        $app = new Application;
        $app->addStage( new Config( $filename, 'ns' ) );
        $app->run();

        $config = array(
            'ns' => array(
                'foo' => array(
                    'bar' => 'baz'
                )
            ),
            'debug' => false
        );

        $this->assertEquals( $config, $app->getConfig() );

        unlink( $filename );
    }

    protected function assertAppIsConfigured( $file, $ext ) {
        $filename = "/tmp/config.{$ext}";
        file_put_contents( $filename, $file );

        $app = new Application;
        $app->addStage( new Config( $filename ) );
        $app->run();

        $config = array(
            'foo' => array(
                'bar' => 'baz'
            ),
            'debug' => false
        );

        $this->assertEquals( $config, $app->getConfig() );

        unlink( $filename );
    }

}

