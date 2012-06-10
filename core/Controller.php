<?php

namespace esprit\core;

/**
 *
 * The primary controller for the framework. This controller creates the initial
 * request object and moves it along from model to view.
 * 
 * @author jbowens
 *
 */
class Controller {
    
    /* The cache to use for storing data in memory between requests */ 
    protected $cache;

	/* The configuration object */
	protected $config;
	
	/**
	 * Creates a new controller from a configuration object.
	 * 
	 * @param Config $config the config object to use
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}
	
	/**
	 * Runs through the entire request to response cycle.
	 */
	public function run() {
		
		
		
	}

	/**
	 * Processes PHP environment variables, instantiating and populating a
	 * Request object to represent the current HTTP request.
	 * 
	 * @return a Request object representing the received HTTP request
	 */
	public function createRequestFromEnvironment() {
	
		$req = new Request(SITE_ID, $_GET, $_POST, $_SERVER['REQUEST_METHOD'], new Url( $_SERVER['REQUEST_URI'] ));
		
		return $req;
		
	}

}


