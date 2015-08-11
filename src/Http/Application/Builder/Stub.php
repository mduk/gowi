<?php

namespace Mduk\Gowi\Http\Application\Builder;

use Mduk\Gowi\Http\Application\Builder as BuilderAbstract;

use Mduk\Gowi\Http\Application;

class Stub extends BuilderAbstract {

  protected $routes = [];

  public function build( Application $app = null, array $config = [] ) {
    $app = parent::build( $app, $config );

    $app->addStage( new \Mduk\Gowi\Http\Application\Stage\Respond );

    $app->setConfig( 'http.response.content_type', $config['content_type'] );
    $app->setConfig( 'http.response.body', $config['body'] );

    return $app;
  }

}
