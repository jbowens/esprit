<?php

namespace esprit\core\debug;

use \esprit\core\CommandResolver;
use \esprit\core\Request;

/**
 * There are default esprit commands that only exist when in debug mode. This 
 * command resolver is used to resolve requests to the debug-mode commands.
 *
 * @author jbowens
 */
class DebugCommandResolver implements CommandResolver {

    protected $controller;

    public function __construct(DebugController $controller) {
        $this->controller = $controller;
    }

    public function resolve(Request $req) {

        $url = $req->getUrl();
        $path = $url->getPath();

        if( $path == "/TranslationTool" )
        {
            require_once('commands/Command_TranslationTool.php');

            $translationTool = new TranslationTool($this->controller->getDatabaseManager(),
                                                   $this->controller->getLanguageSource(),
                                                   $this->controller->createTranslationSource());
            return new commands\Command_TranslationTool( $this->controller->getConfig(),
                                                $this->controller->getDatabaseManager(),
                                                $this->controller->getLogger(),
                                                $this->controller->getCache(),
                                                $this->controller->getViewManager(),
                                                $translationTool );
        }

        return null; 

    }

}
