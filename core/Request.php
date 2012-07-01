<?php

namespace esprit\core;

/**
 * A request to be processed by the website. This class contains information
 * about and GET or POST data sent with the request, HTTP headers received,
 * the url of the request, etc.
 * 
 * @author jbowens
 */
class Request {

    /* The siteid of the site version the request was submitted to */
    protected $siteid;

    /* GET data sent with the request */
    protected $getData = array();
	
    /* POST data sent with the request */
    protected $postData = array();
	
    /* Server environment variables */
    protected $serverData = array();

    /* The request method used in sending the request */
    protected $requestMethod;
	
    /* The URL requested with the page load */
    protected $url;

    /* Dictionary of request headers */
    protected $headers;
	
    /**
     * Creates a new RequestBuilder that can be used to instantiate a
     * Request
     *
     * @return a RequestBuilder
     */
    public static function createBuilder() {
        return new RequestBuilder();
    }

    /**
     * Constructs an empty request object.
     */
    public function __construct(RequestBuilder $builder) {
    	$this->siteid = $builder->getSiteid();
        $this->getData = $builder->getGetData();
        $this->postData = $builder->getPostData();
        $this->serverData = $builder->getServerData();
        $this->requestMethod = $builder->getRequestMethod();
        $this->headers = $builder->getHeaders();
        $this->url = $builder->getUrl();
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
	 * @return boolean  true iff the parameter is set
	 */
    public function getParamExists($getParam) {
        return isset($this->getData[$getParam]);
    }
	
	/**
	 * The key of the POST parameter to retrieve.
	 * 
	 * @param string $param the parameter to retrieve
	 * 
	 * @return  the parameter to return the value for
	 */
    public function getPost($key) {
        if( ! $this->postParamExists($key) )
            return null;
        else
            return $this->postData[$key];
    }
	
	/**
	 * 
	 * Determines whether a given POST parameter exists.
	 * 
	 * @param string $postParam  the parameter to determine existence for
	 * 
	 * @return  true iff the parameter is set
	 */
	public function postParamExists($postParam) {
		return isset($this->postData[$postParam]);
	}
	
    /**
     * Gets the value of an HTTP request header.
     *
     * @param string $key  the header key
     * @return  the header value
     */
    public function getHeader($key) {
        if( ! $this->headerIsSet( $key ) )
            return null;
        else
            return $this->headers[$key];
    }

    /**
     * Determines if a request header was set with the
     * request.
     *
     * @param string $key  the header to check
     * @return  true iff the request sent the given header
     */
    public function headerIsSet($key) {
        return isset($this->headers[$key]);
    }

    /**
     * Returns the URL requested.
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Returns the request method used for this request.
     */
    public function getRequestMethod() {
        return $this->requestMethod;
    }

    public function getIpAddress() {
        return isset($this->serverData['REMOTE_ADDR']) ? $this->serverData['REMOTE_ADDR'] : null;
    }

}

