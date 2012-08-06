<?php

namespace core\esprit\exceptions;

use \InvalidArgumentException;

/**
 * Exception thrown when an attempt is made to index into a collection with
 * an out of bounds index.
 *
 * @author jbowens
 */
class IndexOutOfBoundsException extends InvalidArgumentException {

    protected $accessedIndex;
    protected $itemCount;

    public function __construct($accessedIndex, $itemCount) {
        $this->accessedIndex = $accessedIndex;
        $this->itemCount = $itemCount;
        parent::__construct($this->message = "Attempted to access index " + $this->accessedIndex + " in a collection of size " + $this->itemCount, 0);
    }

}
