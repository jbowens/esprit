<?php

namespace esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode;

class FileNotFound extends HttpStatusCode {

    public function getCode() {
        return 404;
    }

    public function getName() {
        return "File Not Found";
    } 

}
