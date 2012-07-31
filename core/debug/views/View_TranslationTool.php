<?php

namespace esprit\core\debug\views;

use \esprit\core\AbstractView;
use \esprit\core\Response;
use \esprit\core\Request;

class View_TranslationTool extends AbstractView {

    const LOG_SOURCE = "View_TranslationTool";

    public function generateOutput( Response $response )
    {
        $request = $response->getRequest();

        $this->templateParser->loadResponse( $response );

        $template = 'TranslationTool';
        if( $request->getGet('do') == 'create-string' )
            $template = 'TranslationTool_createstring';
            
        $this->templateParser->displayTemplate( $template );
    
    }

}
