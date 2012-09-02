<?php

namespace esprit\core\commands;

use \esprit\core\BaseCommand as BaseCommand;
use \esprit\core\Request as Request;
use \esprit\core\Response as Response;

/**
 * A test index command for right after installation.
 *
 * @author jbowens 
 */
class Command_Index extends BaseCommand {

    const COMMAND_NAME = "Index";
    const LOG_SOURCE = "cmd\\Index";
    
    public function getName() {
        return self::COMMAND_NAME;
    }

    /**
     * See BaseCommand.run(Request $request, Response $response) 
     */
    public function run(Request $request, Response $response) {
        
        return $response;

    }

} 
