<?php

namespace esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode;

class Forbidden extends HttpStatusCode {

    public function getCode() {
        return 403;
    }

    public function getName() {
        return "Forbidden";
    } 

}
