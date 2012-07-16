<?php

require_once "autoloader.php";

$config = esprit\core\Config::createFromJSON("data/config.json");
$controller = new esprit\core\Controller( $config );

// Setup the command resolvers
$pathResolver = $controller->createPathCommandResolver(array('/var/www/commands/'), 'php');
$controller->appendCommandResolver( $pathResolver );

// Setup the view resolvers
$pathViewResolver = $controller->createPathViewResolver(array('/var/www//views/'), 'php');
$controller->appendViewResolver( $pathViewResolver );

$catchall = $controller->createCatchallViewResolver();
$controller->appendViewResolver( $catchall );

// Respond to the user's request
$controller->run();

// Clean up
$controller->close();

