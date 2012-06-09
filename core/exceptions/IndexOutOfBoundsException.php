<?php

namespace core\esprit\exceptions;

/**
 * Exception thrown when an attempt is made to index into a collection with
 * an out of bounds index.
 *
 * @author jbowens
 */
class IndexOutOfBoundsException extends Exception {

    protected $accessedIndex;
    protected $itemCount;

    public function __construct($accessedIndex, $itemCount) {
        $this->accessedIndex = $accessedIndex;
        $this->itemCount = $itemCount;
    }

    public function getMessage() {
        return "Attempted to access index " + $this->accessedIndex + " in a collection of size " + $this->itemCount;
    }
}
