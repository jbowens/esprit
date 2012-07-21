<?php

namespace esprit\core;

use \esprit\core\util\Logger as Logger;

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
    const DEFAULT_PORT = 11211;

    /* The memcached instance supporting the cache */
    protected $memcached;

    /* To prevent multiple calls to memcached for the same key during the same request */
    protected $runtimeCache;

    protected $config;
    protected $logger;

    /* The application-specific key namespace to use */
    protected $keyNamespace;

    /**
     * Creates a new cache.
     */
    public function __construct(array $servers, Config $config, Logger $logger) {

        $this->config = $config;
        $this->logger = $logger;
        $this->memcached = new \Memcached();

        $activeServers = 0;

        foreach( $servers as $server ) {
            $port = $server['port'] ? $server['port'] : DEFAULT_PORT;
            $success = $this->memcached->addServer($server['host'], $port );
            
            if( $success )
                $activeServers++;
            else
                $this->logger->error("Unable to connect to Memcached server " . $server['host'] . ":" . $port, "CACHE");
        }

        if( $activeServers == 0 )
            $this->logger->severe("No active Memcached servers", "CACHE", $servers);

        $this->runtimeCache = array();
        $memcachedSettings = $config->settingExists("memcached") ? $config->get("memcached") : array();
        $this->keyNamespace = isset($memcachedSettings['key_prefix']) ? $memcachedSettings['key_prefix'] : KEY_NAMESPACE;
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
        $qualifiedKey = $this->keyNamespace . $key;
        
        if( strlen($qualifiedKey) > self::MEMCACHED_KEY_LIMIT )
        {
            $this->logger->warning("The cache key '" . $qualifiedKey . "' exceeds the memcached key length limit", "CACHE");
        }

        return $qualifiedKey;
    }

}
