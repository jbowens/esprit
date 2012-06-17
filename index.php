<?php

namespace esprit;

require_once "init.php";

$config = core\Config::createFromXMLFile("config.php");
$controller = new core\Controller( $config );

$controller->run();