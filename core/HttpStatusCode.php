<?php

namespace esprit\core;

/**
 * Enum-ish class for HTTP Status codes
 *
 * @author jbowens
 */
abstract class HttpStatusCode {

    public abstract function getCode();

    public abstract function getName();

    public function getFullStatusString() {
        return $this->getCode() . " " . $this->getName();
    }

}
