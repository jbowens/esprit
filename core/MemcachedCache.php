<?php

namespace esprit\core;

/**
 * A default cache that uses Memcached for caching data
 *
 * As of 2012-06-27, this is an extremely rudimentary cache. It supplies basic
 * key/value association in memory. Additionally functionality may be added in
 * the future but always in a backwards compatible way.
 *
 * @author jbowens 
 */
class MemcachedCache implements Cache {

    /* The memcached instance supporting the cache */
    protected $memcached;

    /**
     * Creates a new cache.
     */
    public function __construct() {
        // TODO: Instantiate memcached instance
    }

    /**
     * Associates a value with a given key in the cache. Optionally, also sets
     * when the association should expire.
     *
     * @param $key  (string) the key to use in the association
     * @param $val  the value to store
     * @param $expire  the expiration time for the association
     */
    public function set( $key, $val, $expire = 0 ) {
        $this->memcached->set($key, $val, $expire);
    }

    /**
     * Retrieves the value associated with the given key.
     *
     * @param $key  the key to lookup
     * @return  the value tied to the given key
     */
    public function get( $key ) {
        return $memcached->get( $key );
    }

}
