<?php

namespace esprit\core\views;

use \esprit\core\AbstractView as AbstractView;
use \esprit\core\Response as Response;

/**
 * This is a default view that most likely most requests will use. It searches the template
 * system for a template that matches the pathname of the requested url. If it finds it,
 * it prints that template, embedded inside the existing DefaultTemplate template.
 *
 * @author jbowens
 */
class DefaultView extends AbstractView {

    const DEFAULT_TEMPLATE = "Default";

    public function generateOutput( Response $response )
    {
        $this->templateParser->loadResponse( $response );

        // TODO: Use requested path for finding the appropriate template

        $this->templateParser->displayTemplate( self::DEFAULT_TEMPLATE );
    }

}
