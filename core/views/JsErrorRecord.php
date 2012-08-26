<?php

namespace esprit\core\views;

use \esprit\core\AbstractView as AbstractView;
use \esprit\core\Response as Response;

/**
 * View for Command_JsErrorRecord
 *
 * @author jbowens
 */
class JsErrorRecord extends AbstractView {

    /**
     * This output probably will never actually be used. It's unclear what the client
     * should really do if it reported the js error incorrectly. The command is implemented
     * such that it will create a hopefully useful error message. In the off chance that the
     * client ever has a use for identifying whether or not an error report was correctly
     * formed, we report the outcome here in json.
     */
    public function generateOutput( Response $response )
    {

        $this->setHeader('Content-Type', 'application/json');

        $json = array();

        if( $response->keyExists("errorMsg") )
        {
            $json['type'] = "error";
            $json['msg'] = $response->get("errorMsg");
        }
        else
        {
            $json['type'] = "ok";
        }

        print json_encode($json);

    }

}
