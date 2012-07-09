<?php

require_once "autoloader.php";

$config = esprit\core\Config::createFromJSON("data/config.json");
$controller = new esprit\core\Controller( $config );

// Setup the command resolvers
$pathResolver = $controller->createPathCommandResolver(array('./commands/'), 'php');
$controller->appendCommandResolver( $pathResolver );

// Respond to the user's request
$controller->run();

// Clean up
$controller->close();

