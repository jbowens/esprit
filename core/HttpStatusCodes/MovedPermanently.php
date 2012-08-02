<?php

namespace esprit\core\HttpStatusCodes;

use \esprit\core\HttpStatusCode;

class MovedPermanently extends HttpStatusCode
{

    public function getCode() {
        return 301;
    }

    public function getName() {
        return "Moved Permanently";
    } 

}
