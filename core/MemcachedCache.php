<?php

namespace esprit\core;

use util\Logger as Logger;

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

    const KEY_NAMESPACE = 'es_';
    const MEMCACHED_KEY_LIMIT = 250;

    /* The memcached instance supporting the cache */
    protected $memcached;

    /* To prevent multiple calls to memcached for the same key during the same request */
    protected $runtimeCache;

    protected $logger;

    /**
     * Creates a new cache.
     */
    public function __construct(Logger $logger) {
        // TODO: Instantiate memcached instance
        $this->runtimeCache = array();
        $this->logger = $logger;
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
        if( isset( $this->runtimeCache[$key] ) )
            unset($this->runtimeCache[$key]);

        $this->memcached->set($this->key($key), $val, $expire);
    }

    /**
     * Retrieves the value associated with the given key.
     *
     * @param $key  the key to lookup
     * @return  the value tied to the given key
     */
    public function get( $key ) {
        if( isset( $this->runtimeCache[$key] ) )
            return $this->runtimeCache[$key];

        $val = $memcached->get( $this->key($key) );

        $this->runtimeCache[$key] = $val;

        return $val;
    }

    /**
     * @see Cache.isCached($key)
     */
    public function isCached( $key ) {
        if( isset($this->runtimeCache[$key]) )
            return true;

        $val = $this->memcached->get( $this->key($key) );

        if( $this->memcached->getResultCode() == Memcached::RES_NOTFOUND )
            return false;
        else {
            $this->runtimeCache[$key] = $val;
            return true;
        }
    }

    /**
     * @see Cache.delete($key)
     */
    public function delete( $key ) {
        if( isset($this->runtimeCache[$key] ) )
            unset($this->runtimeCache[$key]);

        return $memcahced->delete( $this->key($key) );
    }

    /**
     * Retrieves the key to actually use bases on the user-visible key.
     * This is intended as a way to namespace caching keys.
     */
    protected function key( $key ) {
        $qualifiedKey = self::KEY_NAMESPACE . $key;
        
        if( strlen($qualifiedKey) > self::MEMCACHED_KEY_LIMIT )
        {
            $this->logger->warning("The cache key '" . $qualifiedKey . "' exceeds the memcached key length limit", "CACHE");
        }

        return $qualifiedKey;
    }

}
