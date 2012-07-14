<?php

namespace \esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode as HttpStatusCode;

class FileNotFound extends HttpStatusCode {

    public function getCode() {
        return 404;
    }

    public function getName() {
        return "File Not Found";
    } 

}
