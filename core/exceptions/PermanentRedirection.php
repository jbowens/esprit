<?php

namespace esprit\core\exceptions;

/**
 * When thrown, this exception causes a 301 permanent redirection
 * to occur.
 */
class PermanentRedirection extends Exception {

    /* Where to redirect to */
    protected $destination;

    public function __construct($destination) {
        $this->destination = $destination;
    }

}
