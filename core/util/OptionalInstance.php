<?php

namespace esprit\core\util;

/**
 * Contains an instance of an object, which optionally may be null. This class is useful for storing
 * objects in a cache, allowing for differentiating between cache misses and cache hits where the 
 * stored object is null.
 *
 * @author jbowens
 */
class OptionalInstance {

    protected $instance;

    public function __construct( $obj ) {
        $this->instance = $obj;
    }

    /**
     * Gets the contained instance
     */ 
    public function get() {
        return $this->instance;
    }

    /**
     * Sets the contained instance
     */
    public function set( $obj ) {
        $this->instance = $obj;
    } 

}
