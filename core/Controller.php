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

}


