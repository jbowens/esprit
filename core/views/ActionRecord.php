<?php

namespace esprit\core\views;

use \esprit\core\AbstractView;
use \esprit\core\Response;

/**
 * This class manages presentation for the Command_ActionRecord command.
 *
 * @author jbowens
 * @since 2012-08-27
 */
class ActionRecord extends AbstractView
{

    public function generateOutput(Response $response)
    {
        $this->setHeader('Content-Type', 'application/json');
        print json_encode( array( "status" => "ok" ) );
    }

}
