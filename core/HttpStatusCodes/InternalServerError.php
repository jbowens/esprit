<?php

namespace \esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode as HttpStatusCode;

class InternalServerError extends HttpStatusCode {

    public function getCode() {
        return 500;
    }

    public function getName() {
        return "Internal Server Error";
    } 

}
