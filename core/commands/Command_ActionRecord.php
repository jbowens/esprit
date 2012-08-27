<?php

namespace esprit\core\commands;

use \esprit\core\BaseCommand as BaseCommand;
use \esprit\core\Request as Request;
use \esprit\core\Response as Response;

/**
 * A command that is called through client ajax to record user
 * interaction with the page.
 *
 * @author jbowens 
 */
class Command_ActionRecord extends BaseCommand {

    const COMMAND_NAME = "ActionRecord";

    /**
     * See BaseCommand.run(Request $request, Response $response) 
     */
    public function run(Request $request, Response $response) {

        if( ! $request->postParamExists('identifier') )
        {
            $this->error("Received an action record request without an identifier");
        }
        else
        {
            // TODO: Implement action recording

        }

        return $response;
    }

    public function getName() {
        return self::COMMAND_NAME;
    }

} 
