<?php

namespace esprit\core\exceptions;

/**
 * An exception thrown when the server is incapable of servicing
 * a request. This is considered a fatal exception and will result
 * in termination of the command flow.
 *
 * @author jbowens 
 */
class NonserviceableRequestException extends \RuntimeException { 

    protected $request;

    public function __construct( \esprit\core\Request $request )
    {
        $this->request = $request;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getMessage() {
        return "Received an unserviceable request from the user to " . $this->request->getUrl()->getPath();
    }

}
