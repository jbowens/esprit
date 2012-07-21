<?php

namespace esprit\core\commands;

use \esprit\core\BaseCommand as BaseCommand;
use \esprit\core\Request as Request;
use \esprit\core\Response as Response;

/**
 * A default fallback command that should be used when no command matches a given
 * request.
 *
 * @author jbowens 
 */
class Command_DefaultFallback extends BaseCommand {

    /**
     * See BaseCommand.run(Request $request, Response $response) 
     */
    public function run(Request $request, Response $response) {
        
        $response->set('IS_404', true);
        
        return $response;

    }

} 
