<?php

namespace esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode;

/**
 * Requires HTTP/1.1
 */
class TemporaryRedirect extends HttpStatusCode
{

    public function getCode() {
        return 307;
    }

    public function getName() {
        return "Temporary Redirect";
    } 

}
