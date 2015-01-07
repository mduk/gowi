# Gowi

![Travis Build Status](https://api.travis-ci.org/mduk/gowi.svg)

Gowi aims to keep it simple, to be flexible, and to be lightweight. Most importantly however, Gowi hopes to find the middle ground between all those things.

There are Three main concepts at work in Gowi. The Application, Services, and Stages.

## Services

Simply put, these are objects that do things for you. There are two main charactistics of these objects that make them Services in the Gowi definition.

* **Services have no access to the Application.**
* **Services have no access to the HTTP Request or Response.**

This means that services can't read config or call other services from the Application, they can't add stages, they have no involvement with the Application object *whatsoever*. Everything else is up to you.

If a service needs some configuration, then a bootstrap Stage should read the config from the Application and pass it to the service object after it has been instantiated. Likewise if a Service requires a reference to another Service, then there should be a Stage to capture that when the Service is initialised.

This helps to keep our dependencies visible and maintain a divide between your Application and your Domain.

## Stages

Stages encapsulate all the things that need to happen sequentially in order to fulfil a Request and populate a Response.

Stages are objects that implement the `Mduk\Gowi\Application\Stage` interface.  When invoked, they are passed references to the Application, Request and Response objects.

By passing the Application object to Stages, we are allowing them access to all the Application's resources, namely Configuration and Services. By passing the Request, they all have immediate access to the input data, and by passing a *shared, persistent* Response instance to each of them, the Response can be built up incrementally. A great example of this is that a single stage can read query parameters and set response cookies early on, leaving your controller care-free.

Just as a Response can be built incrementally, a Response can also be handled incrementally. Maybe you want to handle all your GET requests first before you bother connecting to the write database?

Because stages are just things that happen in order, there's absolutely no distinction between a bootstrapper and a controller beyond a line on your whiteboard. This makes it easy to reorder stages and create multi-tierd applications.

## Application

The Application is the backbone that holds all the Services and Stages together. It is the thing that invokes all the stages in order and makes two resources available to them. Services and Configuration. Services we've already covered.

Configuration is as we've always known it to be, it allows us to easily modify our application's behaviour without having to rebuild it. Most of the time when you say "confiugration", it's followed by the word "file". So the term tends to conjure up images of immutability, `const` and "fixed files". However in the same way that we can build our Response incrementally, so too can we configure.

Default values would be read first. Then read a file that defines some server info. Maybe another file for db credentials. Read some values from cache and the session. User settings and permissions could be read from a Service. Immutable values are set last.

# Super-simple Example

Now for the obligatory super-simplified example of everything in one file, no doubt riddled with bad habits.

    <?php // change_user_name.php
    
    use PDO;
    
    use Mduk\Gowi\Application;
    use Mduk\Gowi\Application\Stage\Stub as StubStage;
    
    use YourCompany\\Domain\\User\\Service as UserService;
    use YourCompany\\Domain\\User\\Db as UserDb;
    
    /**
     * This is a stupid-simple application.
     *
     * A stand-alone script that expects a POST request with two vars, old_name and new_name.
     * It changes user names and the form's on a page somewhere else.
     */
    
    $app = new Application;
    
    /**
     * If we called $app->run() now, it wouldn't achieve much since it has nothing to do.
     * 
     * Thinking about the lifecycle of the app, what does it do? Typically we can divide it
     * into two stages.
     *
     * Stage One: Bootstrap the application.
     * Stage Two: Fulfil the request.
     */
     
     /**
      * So we add a bootstrap stage, all it needs to do is set up a database connection and
      * our User Service.
      */
    $app->addStage( new StubStage( function( Application $app, Request $req, Response $res ) {
    	
    	/**
    	 * Typically we'll set some config so let's do that. We can safely
    	 * assume that the config file contains some db credentials.
    	 */
    	 $app->setConfig( json_parse( file_get_contents( 'config.json' ) ) );
    	
    	/**
    	 * This is the Service. It's entirely your own.
    	 * In this case it's an object that takes a PDO connection,
    	 * presumably to the user database, and has a method on it
    	 * that we'll call later.
    	 */
    	$userService = new UserService( new PDO( $app->getConfig( 'userdb' ) ) );
    	
    	/**
    	 * Register the Service with the Application under a name.
    	 */
    	$app->registerService( 'user', $userService );
    	
    	/**
    	 * Done bootstrapping the application. If we get this far, no exceptions were thrown
    	 * everything's good. We return null to tell the application to continue to the next
    	 * stage.
    	 *
    	 * If that seems counter-intuitive, think of it this way: The application has a list
    	 * of things to do, and is lazy. Each time it executes a stage, it's asking "Do you
    	 * have a response for me?". We're just saying no. The application then tries it's
    	 * luck with the next stage. If we returned $res, then the application wouldn't
    	 * bother to run the remaining stages.
    	 */
    	 return null;
    } ) );
    
	/**
     * This is the main show. This is akin to your Controller Action in Symfony or Zend. This
     * stage will always get executed, because the prior stage always returns null (unless it
     * throws an exception of course).
     */
    $app->addStage( new StubStage( function( Application $app, Request $req, Response $res ) {
    	
    	/**
    	 * Pull some data out of the request.
    	 */
    	$oldName = $req->request->get( 'old_name' );
    	$newName = $req->request->get( 'new_name' );
    	
    	/**
    	 * Call on the User Service to do the thing we wanted, change the user name.
    	 * In this case we assume that the changeUserName method will validate it's parameters
    	 * and will throw an exception if either of them are unacceptable.
    	 */
    	$app->getService( 'user' )->changeUserName( $oldName, $newName );
    	
    	/**
    	 * Prepare a response. Not a whole lot to do here, so we just give some indication
    	 * that it worked and the app didn't white-screen-of-untimely-death on us.
    	 */
    	$res->setStatusCode( 200 );
    	$res->setContent( '<html><body><h1>Username was changed</h1></body></html>' );
    	
    	
    	return $res;
    } ) );
    
    $app->run()->respond();
 
# Dev Environment & Building

A development environment is provided in the `cookbook/` directory, I'm practicing chef.
Use `kitchen converge` to build and provision the box, and `kitchen login` to get going.
The project root is mounted under `/tmp/src`.
A Makefile is included to wrap up the whole process of building and testing for dev purposes.

# License & Copyright

Released under the MIT license which can be found in the LICENSE file.

Copyright 2014-2015 Daniel Kendell <daniel@kendell.org.uk>

