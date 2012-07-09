<?php

namespace esprit;

require_once "core/Controller.php";

/**
 * This file adds an autoloader for all default esprit classes.
 */
spl_autoload_register(__NAMESPACE__.'\core\Controller::autoload');

