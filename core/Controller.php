<?php

namespace esprit\core;

/**
 * The primary controller for the framework. This controller creates the initial
 * request object and moves it along from model to view.
 * 
 * @author jbowens
 */
class Controller {
    
    /* The cache to use for storing data in memory between requests */ 
    protected $cache;

	/* The configuration object */
	protected $config;

    /* The logger used for logging major events */
    protected $logger;

    /* The command resolvers to use */
    protected $commandResolvers();
	
	/**
	 * Creates a new controller from a configuration object.
	 * 
	 * @param Config $config the config object to use
	 */
	public function __construct(Config $config) {
		$this->config = $config;
        $this->logger = util\Logger::newInstance();
        $this->commandResolvers = array();
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
    public function createPathCommandResolver($directories, $ext = null) {
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
	 * Runs through the entire request to response cycle.
	 */
	public function run() {
	
        $this->initializeSessions();
    	
	    $request = $this->createRequestFromEnvironment();	

        $command = null;
        foreach( $commandResolvers as $resolver ) {
            $command = $resolver->resolve($request);
            if( $command != null )
                break;
        }

        if( $command == null ) {
            //TODO: Use NoMatchingCommand command
        }
	
        try {
            $command->execute();
        } catch( Exception $e ) {
            // TODO: Add more granular logging and add
            // logic for actually handling exceptions
            $this->logger->logEvent( LogEventFactory::createFromException( $e, $command->getName() ) );
        }
    	
	}

	/**
	 * Processes PHP environment variables, instantiating and populating a
	 * Request object to represent the current HTTP request.
	 * 
	 * @return a Request object representing the received HTTP request
	 */
	public function createRequestFromEnvironment() {

        $req = (Request::createBuilder())->siteid(SITE_ID)->getData($_GET)->postData($_POST)
               ->requestMethod($_SERVER['REQUEST_METHOD'])->url(new Url( $_SERVER['REQUEST_URI'] ))
               ->headers(getallheaders())->build();	
		
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
       if( ! $config->settingExists("session_handler") || $config->get("session_handler") == "default" )
           return new SessionHandler();
       else
           return new SessionHandler();
       // TODO: Update line above 
    }

    /**
     * Sets session preferences and begins the session.
     */
    protected function initializeSessions() {
        SessionHandlerInterface $sessionHandler = $this->getSessionHandler();
        session_set_save_handler( $sessionHandler );
        session_start();
    }

}

