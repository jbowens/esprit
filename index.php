<?php

namespace esprit;

require_once "init.php";

$config = core\Config::createFromJSON("data/config.json");
$controller = new core\Controller( $config );

$controller->run();
