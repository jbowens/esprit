<?php

namespace esprit\core;

/**
 * A cache interface used throughout the framework. The default implementation
 * of this interface is the MemcachedCache class.
 *
 * @author jbowens
 */
interface Cache {

    /**
     * Retrieves the value associated with the given key.
     *
     * @param $key  the key to lookup
     * @return  the value tied to the given key
     */
    public function get( $key );

    /**
     * Associates a value with a given key in the cache. Optionally, also sets
     * when the association should expire.
     *
     * @param $key  (string) the key to use in the association
     * @param $val  the value to store
     * @param $expire  the expiration time for the association
     */
    public function set( $key, $val, $expire = 0 );

    /**
     * Deletes the key, value association with the given key from the cache.
     *
     * @param $key  (string) the key of the association to delete
     */
    public function delete($key);

    /**
     * Determines is a key/val pair is cached with the given key.
     *
     * @param $key  (stirng) the key to check
     * @return true iff the key is set in the cache
     */
    public function isCached($key);

    /**
     * Accesses the given namespace of the cache.
     *
     * @param string $key  the namespace to access
     * @return Cache  a cache object in the given namespace
     */
    public function accessNamespace( $namespace );

    /**
     * Returns the namespace of this cache.
     *
     * @return String  this cache's root namespace
     */
    public function getNamespace();

}
