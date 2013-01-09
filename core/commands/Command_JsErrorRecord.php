<?php

namespace esprit\core\commands;

use \esprit\core\BaseCommand as BaseCommand;
use \esprit\core\Request as Request;
use \esprit\core\Response as Response;

/**
 * A command that is called through client ajax to record javascript
 * errors that occur. Errors are entered into the logs making both
 * client and server side errors available through the esprit error
 * logs.
 *
 * @author jbowens 
 */
class Command_JsErrorRecord extends BaseCommand {

    const COMMAND_NAME = "JsErrorRecord";
    const LOG_SOURCE = "JS";

    /**
     * See BaseCommand.run(Request $request, Response $response) 
     */
    public function run(Request $request, Response $response) {
        
        $this->error("(".$request->getPost('path')."; " . $request->getServer('HTTP_USER_AGENT') ." ) " . $request->getPost('eName') . ": " . $request->getPost('eMsg') . $request->getPost('eStack'), self::LOG_SOURCE);
        
        return $response;

    }

    public function getName() {
        return self::COMMAND_NAME;
    }

} 
