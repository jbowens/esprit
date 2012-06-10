<?php

namespace esprit\core;

/**
 * A request builder, required to instantiate a Request object.
 *
 * @author jbowens
 */
class RequestBuilder {

    /* Request properties */
    protected $siteid;
    protected $getData = array();
    protected $postData = array();
    protected $requestMethod;
    protected $url;

    public function __construct() {
    }

    /**
     * Constructs a new Request object with this builder's properties.
     */
    public function build() {
        return new Request( $this );
    }

    /* Getters */

    public function getSiteid() {
        return $this->siteid;
    }

    public function getGetData() {
        return $this->getData;
    }

    public function getPostData() {
        return $this->postData;
    }

    public function getRequestMethod() {
        return $this->requestMethod;
    }

    public function getUrl() {
        return $this->url;
    }

    /* Setters */

    public function siteid( $newId ) {
        $this->siteid = $newId;
        return $this;
    }

    public function getData( $newGetData ) {
        $this->getData = $newGetData;
        return $this;
    }

    public function postData( $newPostData ) {
        $this->postData = $newPostData;
        return $this;
    }
    
    public function requestMethod( $reqMethod ) {
        $this->requestMethod = $reqMethod; 
        return $this;
    }

    public function url( $newUrl ) {
        $this->url = $newUrl;
        return $this;
    }

}
