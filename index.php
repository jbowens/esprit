<?php

namespace esprit;

$config = core\Config::createFromJSON("data/config.json");
$controller = new core\Controller( $config );

// Setup the command resolvers
$pathResolver = $controller->createPathCommandResolver('./commands/', 'php');
$controller->appendCommandResolver( $pathResolver );

// Respond to the user's request
$controller->run();

// Clean up
$controller->close();

