<?php

namespace \esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode as HttpStatusCode;

class Forbidden extends HttpStatusCode {

    public function getCode() {
        return 403;
    }

    public function getName() {
        return "Forbidden";
    } 

}
