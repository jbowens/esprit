<?php

namespace esprit\core\views;

use \esprit\core\AbstractView as AbstractView;
use \esprit\core\Response as Response;
use \esprit\core\HttpStatusCodes\InternalServerError as InternalServerError;

/**
 * This is a fallback view for when an appropriate view could not be found.
 * This should be considered an error case. There should also be a view
 * for any possible Response object state. This View is implemented to
 * throw a 500 Internal Server Error status and display a simple message
 * to the user.
 *
 * @author jbowens
 */
class FallbackView extends AbstractView {

    public function generateOutput( Response $response )
    {
        $this->setStatus( new InternalServerError() ); 

    }


}
