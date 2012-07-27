<?php

namespace esprit\core;

/**
 * A cache that saves nothing. Useful stuff.
 *
 * @author jbowens
 */
class BlackholeCache implements Cache {

    public function get( $key ) {
        return null;
    }

    public function set($key, $val, $expire = 0) {
        return false;
    }

    public function delete($key) {
        return false;
    } 

    public function isCached($key) {
        return false;
    } 

}
