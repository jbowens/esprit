<?php

namespace esprit\core\exceptions;

/**
 * An exception thrown when trying to index into a data set with a key
 * that is not set.
 *
 * @author jbowens
 */
class NonexistentKeyException extends Exception {

    protected $accessedKey;

    public function __construct( $accessedKey ) {
        $this->accessedKey = $accessedKey;
    }

    public function getMessage() {
        return "The key '" + $this->accessedKey + "' is not set.";
    }

}
