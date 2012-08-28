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
 * WARNING: If you call isCached or get, this class will also maintain a reference
 * to the returned object. This means that the returned object will not be garbage
 * collected until the cache object is garbage collected! If you need to destroy
 * a reference, call 'removeFromLocalCache'. 
 *
 * @author jbowens 
 */
class MemcachedCache implements Cache {
    use LogAware;

    const LOG_SOURCE = "MEMCACHED";
    const APP_NAMESPACE = 'es';
    const NAMESPACE_SEPARATOR = '\\';
    const MEMCACHED_KEY_LIMIT = 250;
    const DEFAULT_PORT = 11211;

    /* The memcached instance supporting the cache */
    protected $memcached;

    /* To prevent multiple calls to memcached for the same key during the same request */
    protected $runtimeCache;

    protected $config;
    protected $logger;

    /* The cache namespace to use */
    protected $namespace;

    /**
     * Creates a new cache.
     */
    public static function connectToMemcached(array $servers, Config $config, Logger $logger) {

        $memcached = new \Memcached();

        $activeServers = 0;

        foreach( $servers as $server ) {
            $port = $server['port'] ? $server['port'] : self::DEFAULT_PORT;
            $success = $memcached->addServer($server['host'], $port );
            
            if( $success ) {
                $activeServers++;
                $logger->finer("Connected to memcached server " . $server['host'] . ":".$port, self::LOG_SOURCE);
            }
            else
                $logger->error("Unable to connect to Memcached server " . $server['host'] . ":" . $port, self::LOG_SOURCE);
        }

        if( $activeServers <= 0 ) {
            $logger->severe("No active Memcached servers", $servers, self::LOG_SOURCE);
        }

        return new MemcachedCache($memcached, $config, $logger);
        
    }

    /**
     * Do not use this constructor directly. Use MemcacedCache::connectToMemcached(...)
     */
    public function __construct(\Memcached $memcached, Config $config, Logger $logger, $namespace = null)
    {
        $this->runtimeCache = array();
        
        $memcachedSettings = $config->settingExists("memcached") ? $config->get("memcached") : array();
        if( $namespace == null || $namespace == "" )
            $namespace = isset($memcachedSettings['key_prefix']) ? $memcachedSettings['key_prefix'] : APP_NAMESPACE;
        $this->namespace = $namespace;
        
        $this->memcached = $memcached;
        $this->config = $config;
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
        $success = $this->memcached->set($this->key($key), $val, $expire);
        if( ! $success )
            $this->warning("Unable to save cache key " . $key . ", memcached message: " . $this->memcached->getResultMessage());
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

        $val = $this->memcached->get( $this->key($key) );

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

        if( $this->memcached->getResultCode() == \Memcached::RES_NOTFOUND )
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

        return $this->memcached->delete( $this->key($key) );
    }

    /**
     * Retrieves the key to actually use bases on the user-visible key.
     * This is intended as a way to namespace caching keys.
     */
    protected function key( $key ) {
        $qualifiedKey = $this->namespace . $key;
        
        if( strlen($qualifiedKey) > self::MEMCACHED_KEY_LIMIT )
        {
            $this->warning("The cache key '" . $qualifiedKey . "' exceeds the memcached key length limit");
        }

        return $qualifiedKey;
    }

    /**
     * @see Cache.accessNamespace()
     */
    public function accessNamespace( $namespace ) {
        if( $namespace[0] == self::NAMESPACE_SEPARATOR )
            $absoluteNamespace = $namespace;
        else
            $absoluteNamespace = $this->namespace . self::NAMESPACE_SEPARATOR . $namespace;

        return new MemcachedCache( $this->memcached, $this->config, $this->logger, $absoluteNamespace);
    }

    /**
     * @see Cache.getNamespace()
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Destroys any local runtime reference to the object at
     * the given key.
     *
     * @param $key the key to remove from the local cache
     */
    public function removeFromLocalCache( $key )
    {
        if( isset($this->runtimeCache[$key]) ) {
            unset($this->runtimeCache[$key]);
        }
    }

}
