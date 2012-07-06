<?php

namespace esprit\core;

use esprit\core\util\Logger as Logger;

/**
 * The ViewManager handles the presentation of responses. All output should go
 * through this ViewManager.
 *
 * @author jbowens
 */
class ViewManager {

    protected $logger;

    protected $viewResolvers;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
        $this->viewResolvers = array();
    }

    /**
     * Displays the given output object.
     */
    public function display(Output $output) {

        // Find the appropriate view
        $view = null;
        $i = 0;
        while( $i < count($this->viewResolvers) && $view == null )
        {
            $view = $this->viewResolvers[$i]->resolve($output);
            $i++;
        }

        if( $view == null ) {
            $this->logger->error("No matching view found",  'ViewManager', $output);
            // TODO: Use an appropriate default view... 404? 500 internal server error? 
        }


    }

    /**
     * Adds a view resolver to the list of resolvers used when pairing Output objects with
     * views.
     */
    public function addViewResolver(ViewResolver $viewResolver) {
        array_push($this->$viewResolvers, $viewResolver);
    }

}
