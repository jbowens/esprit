<?php

namespace esprit\core;

/**
 * Represents a server response to a request. This is an internal response object and does
 * not immediately map to an HTTP response.
 *
 * @author jbowens
 */
class Response {

    /* The request that this is a response to */
    protected $request;

    /* A store of computed values to be passed on to the view */
    protected $output = array();

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function getRequest() {
        return $this->request;
    }

    /**
     * Sets a response data value.
     *
     * @param $key
     * @param $val
     */
    public function set($key, $val) {
        $this->output[$key] = $val;
    }

    /**
     * Gets the value associated with the key.
     *
     * @param $key  the key to retrieve
     */
    public function get($key) {
        if( ! isset( $this->output[$key] ) )
            return null;
        return $this->output[$key];
    }

    /**
     * Determines whether the given key is set.
     *
     * @param $key  the key to check
     */
    public function __isset($key) {
        return isset($output[$key]);
    }

    /**
     * Alias for get()
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Alias for set()
     */
    public function __set($key, $val) {
        return $this->set($key, $val);
    }

    public function getAsArray() {
        return $this->output;
    }
}


