<?php

namespace esprit\core;

/**
 * 
 * A request to be processed by the website. This class contains information
 * about and GET or POST data sent with the request, HTTP headers received,
 * the url of the request, etc.
 * 
 * @author jbowens
 *
 */
class Request {

	/* GET data sent with the request */
	protected $getData = array();
	
	/* POST data sent with the request */
	protected $postData = array();
	
	/**
	 * Processes PHP environment variables, instantiating and populating a
	 * Request object to represent the current HTTP request.
	 * 
	 * @return a request object representing the received HTTP request
	 */
	public static function createRequestFromEnvironment() {
		
	}
	
	/**
	 * A protected constructor. This should not--and cannot--be used to create a new
	 * request.
	 */
    protected function __construct() {
    	
    }

    /**
     * The key of the GET parameter to retrieve.
     * 
     * @param string $key  the get parameter to retrieve
     */
	public function getGet($key) {
		if( ! $this->getParamExists($key) )
			return null;
		else
			return $this->getData[$key];
	}
	
	/**
	 * Determines whether a given GET parameter exists.
	 * 
	 * @param string $param  the parameter to determine existence for
	 * 
	 * @return boolean  true iff the paramaeter is set
	 */
	public function getParamExists($getParam) {
		return isset($this->getData[$getParam]);
	}
	
	/**
	 * The key of the POST parameter to retrieve.
	 * 
	 * @param string $param the parameter to determine existence for
	 * 
	 * @return  the parameter to return the value for
	 */
	public function getPost($key) {
		if( ! $this->postParamExists($key) )
			return null;
		else
			return $this->postData[$key];
	}
    
}

