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
	
	/**
	 * Creates a new controller from a configuration object.
	 * 
	 * @param Config $config the config object to use
	 */
	public function __construct(Config $config) {
		$this->config = $config;
        $this->logger = util\Logger::newInstance();
	}
	
	/**
	 * Runs through the entire request to response cycle.
	 */
	public function run() {
	
        $this->initializeSessions();
    	
	    $request = $this->createRequestFromEnvironment();	
		
	}

	/**
	 * Processes PHP environment variables, instantiating and populating a
	 * Request object to represent the current HTTP request.
	 * 
	 * @return a Request object representing the received HTTP request
	 */
	public function createRequestFromEnvironment() {

        $req = (Request::createBuilder())->siteid(SITE_ID)->getData($_GET)->postData($_POST)
               ->requestMethod($_SERVER['REQUEST_METHOD'])->url(new Url( $_SERVER['REQUEST_URI'] ))->build();	
		
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
        SessionHandlerInterface sessionHandler = $this->getSessionHandler();
        session_set_save_handler( sessionHandler );
        session_start();
    }

}

