<?php

namespace core\exceptions;

use \InvalidArgumentException;

/**
 * An exception for dealing with malformed urls.
 *
 * @author jbowens
 */
class MalformedUrlException extends InvalidArgumentException {

	/* The malformed url that was received */
    protected $malformedUrl;

    public function __construct($malformedUrl) {
        $this->malformedUrl = $malformedUrl;
    }

    public function getMessage() {
        return "The url '" + $this->malformedUrl + "' is malformed";
    }

}
