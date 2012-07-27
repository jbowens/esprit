<?php

namespace esprit\core\debug\views;

use \esprit\core\AbstractView;
use \esprit\core\Response;

class View_TranslationTool extends AbstractView {

    const LOG_SOURCE = "View_TranslationTool";

    public function generateOutput( Response $response )
    {
        $this->templateParser->loadResponse( $response );
        die('translation tool!');
    }

}
