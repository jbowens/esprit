<?php

namespace esprit\core;

use \Exception;
use \SessionHandler as SessionHandler;
use \SessionHandlerInterface as SessionHandlerInterface;
use \ReflectionClass as ReflectionClass;

use \esprit\core\util\LogEventFactory;
use \esprit\core\util\Logger;
use \esprit\core\exceptions\UnserviceableRequestException as UnserviceableRequestException;

/**
 * The primary controller for the framework. This controller creates the initial
 * request object and moves it along from model to view.
 * 
 * @author jbowens
 */
class Controller {

    const LOG_ORIGIN = "CONTROLLER";
    const DEFAULT_FALLBACK_COMMAND = '\esprit\core\commands\Command_DefaultFallback';
    const DEFAULT_TIMEZONE = 'America/New_York';

    /* The cache to use for storing data in memory between requests */ 
    protected $cache;

	/* The configuration object */
	protected $config;

    /* The logger used for logging major events */
    protected $logger;

    /* The datbase manager used by the controller */
    protected $dbm;

    /* The command resolvers to use */
    protected $commandResolvers;
	
    /* An optional custom session handler */
    protected $customSessionHandler;

	/**
	 * Creates a new controller from a configuration object.
	 * 
	 * @param Config $config the config object to use
	 */
	public function __construct(Config $config) {
		$this->config = $config;
        $this->logger = util\Logger::newInstance();
        $this->cache = new MemcachedCache($config->get('memcached_servers') ? $config->get('memcached_servers') : array(), $this->logger);
        $this->commandResolvers = array();
        $this->customSessionHandler = null;

        $this->dbm = new db\DatabaseManager($config->get("db_default_dsn"),
                                            $config->get("db_default_user"),
                                            $config->get("db_default_pass"),
                                            $this->logger);
	}

    /**
     * Creates a new PathCommandResolver for BaseCommands using this contorller's
     * config, logger and db manager.
     *
     * @see PathCommandResolver::__construct()
     *
     * @param $directories  list of directories to search in
     * @param $ext  (optional) the extension to expect php files to have
     * @return a PathCommandResolver
     */
    public function createPathCommandResolver(array $directories = array(), $ext = null) {
        return new PathCommandResolver($this->dbm, $this->config, $this->logger, $directories, $ext); 
    }
	
    /**
     * Creates a new XmlCommandResolver for BaseCommands using this controller's
     * config, logger and db manager.
     *
     * @see XmlCommandResolver::__construct()
     *
     * @param $filepath
     * @param $classpath
     * @param $extension
     * @return a XmlCommandResolver  
     */
    public function createXmlCommandResolver($filepath, $classpath, $extension = null) {
        return new XmlCommandResolver($this->dbm, $this->config, $this->logger, $filepath, $classpath, $extension);
    }

    /**
     * Appends a new command resolver onto the list of resolvers to use. Since resolvers
     * are used in the order that they're appended, you should make note of the order
     * you insert command resolvers.
     *
     * CommandResolver $resolver  a new resolver to use
     */
    public function appendCommandResolver(CommandResolver $resolver) {
        array_push($this->commandResolvers, $resolver);
    }

    /**
     * Frees up any system resources being held and prepares for exiting. You should always
     * call this when you're done with a Controller instance. After calling this method, you
     * cannot trust any Controller instantiated objects to still be in a usuable state as 
     * database connections, file handles, etc will all have been closed.
     */
    public function close() {
        try {
            $this->dbm->close();
        } catch( Exception $ex ) {
            $this->error($ex, 'closing DatabaseManager');
        }

        // Close any log recorders listening to the default logger
        try {
            foreach( $this->logger->getRecorders() as $recorder )
                $recorder->close();
        } catch( Exception $ex ) {
            // Nothing we can do. The recorders are all gone.
        }

        try {
            $this->logger->close();
        } catch( Exception $ex ) {
            // Well shit. 
        }
    }

	/**
	 * Runs through the entire request to response cycle.
	 */
	public function run() {

        try {

            // Set the timezone
            if( $this->config->settingExists('default_timezone') )
                date_default_timezone_set($this->config->get('default_timezone'));
            else
                date_default_timezone_set(self::DEFAULT_TIMEZONE);

            $this->initializeSessions();
            
            $request = $this->createRequestFromEnvironment();
            $response = new Response();

            $this->logger->finest("Request from " . $request->getIpAddress() . " " . date("r"), self::LOG_ORIGIN);

            // Identify the command that should be run
            $command = null;
            foreach( $this->commandResolvers as $resolver ) {
                $command = $resolver->resolve($request);
                if( $command != null )
                    break;
            }

            // If the request wasn't resolved to command, use the fallback.
            if( $command == null )
            {
                if( $this->config->settingExists('FallbackCommand') ) {
                    $fallbackCmdName = $this->config->get('FallbackCommand');
                } else {
                    $fallbackCmdName = self::DEFAULT_FALLBACK_COMMAND;
                }

                $class = new ReflectionClass( $fallbackCmdName );
                if( ! $class->isSubclassOf('\esprit\core\BaseCommand') || ! $class->isInstantiable() )
                    throw new UnserviceableRequestException( $request );
                
                $command = $class->newInstance($this->config, $this->dbm, $this->logger);

                $this->logger->warning('Hit fallback command on request to ' . $request->getUrl()->getPath() , self::LOG_ORIGIN, $request); 
            }
        
            try {
                $response = $command->execute($request, $response);
            } catch( Exception $e ) {
                // TODO: Add more granular logging and add
                // logic for actually handling exceptions
                $this->logger->logEvent( LogEventFactory::createFromException( $e, $command->getName() ) );
            }

        } catch( UnserviceableRequestException $exception ) {
            // Log this
            $this->logger->logEvent( LogEventFactory::createFromException( $exception, self::LOG_ORIGIN ) );
            $this->dieGracefully();
        } catch( Exception $exception ) {
            // Don't expose internal details of the exception to the user. Just exit.
            return false;
        }

        return true;
            
	}

	/**
	 * Processes PHP environment variables, instantiating and populating a
	 * Request object to represent the current HTTP request.
	 * 
	 * @return a Request object representing the received HTTP request
	 */
	public function createRequestFromEnvironment() {

        //TODO: Update with support for actual site id 
        $url = new Url($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);
        $req = Request::createBuilder()->siteid(1)->getData($_GET)->postData($_POST)
               ->requestMethod($_SERVER['REQUEST_METHOD'])->url( $url )
               ->headers(getallheaders())->serverData($_SERVER)->build();	
		
		return $req;
		
	}

    /**
     * Returns the logger used by the controller.
     *
     * @return util\Logger  the controller's logger
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Retrieves the session handler that should be used by the controller.
     *
     * @return a SessionHandlerInterface object
     */
    public function getSessionHandler() {
        if( $this->customSessionHandler != null)
            return $this->customSessionHandler;
        else
            return new SessionHandler();
    }

    /**
     * Sets a custom session handler to use. If this function is not called, the
     * default session handler defined in the php.ini will be used.
     *
     * @param SessionHandlerInterface $sessionHandler
     */
    public function setCustomSessionHandler(SessionHandlerInterface $sessionHandler) {
        $this->customSessionHandler = $sessionHandler;
    }

    /**
     * Sets session preferences and begins the session.
     */
    protected function initializeSessions() {
        $sessionHandler = $this->getSessionHandler();
        session_set_save_handler( $sessionHandler );
        session_start();
    }

    /**
     * Logs an error that occurred within the controller.
     *
     * @param Exception $ex  the exception of the error
     * @param $sourceDesc  a string describing the source of the error
     */
    protected function error(Exception $ex, $sourceDesc) {
        $this->logger->log( LogEventFactory::createFromException($ex, $sourceDesc) ); 
    }

    /**
     * This method may be called to abruptly end excecution but still print
     * an error message back to the user.
     */
    protected function dieGracefully( $message = null ) {

        if( ! $message )
            $message = 'The server was unable to service your request.';

        http_response_code(500);
        
        $html = "<!DOCTYPE html>
                   <html>
                    <head>
                     <title>Internal Server Error</title>
                    </head>
                    <body>
                     <h1>Internal Server Error</h1>
                     <p>" . $message . "</p>
                    </body>
                    </html>";
        die( $html );
    }

}

