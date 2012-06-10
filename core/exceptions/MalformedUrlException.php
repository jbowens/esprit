<?php

namespace core\exceptions;

/**
 * An exception for dealing with malformed urls.
 *
 * @author jbowens
 */
class MalformedUrlException extends Exception {

    protected $malformedUrl

    public function __construct($malformedUrl) {
        $this->malformedUrl = $malformedUrl;
    }

    public function getMessage() {
        return "The url '" + $this->malformedUrl + "' is malformed";
    }

}
