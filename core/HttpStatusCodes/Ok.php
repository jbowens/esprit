<?php

namespace esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode;

class Ok extends HttpStatusCode {

    public function getCode() {
        return 200;
    }

    public function getName() {
        return "OK";
    } 

}
