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

    protected $config;

    protected $logger;

    protected $viewResolvers;

    protected $templateParser;

    public function __construct(Config $config, Logger $logger) {
        $this->config = $config;
        $this->logger = $logger;
        $this->viewResolvers = array();

        // TODO: Add support for other template parsers besides Twig
        $this->templateParser = new TwigTemplateParser($config, $logger);
    }

    /**
     * Displays the given response object.
     */
    public function display(Response $response) {

        // Find the appropriate view
        $view = null;
        $i = 0;
        while( $i < count($this->viewResolvers) && $view == null )
        {
            $view = $this->viewResolvers[$i]->resolve($response);
            $i++;
        }

        if( $view == null ) {
            $this->logger->error("No matching view found", 'ViewManager', $response);
            // TODO: Use an appropriate default view... 404? 500 internal server error? 
        }

        $view->display( $response );

    }

    /**
     * Adds a view resolver to the list of resolvers used when pairing Response objects with
     * views.
     */
    public function addViewResolver(ViewResolver $viewResolver) {
        array_push($this->viewResolvers, $viewResolver);
    }

    /**
     * Retrieves the template parser used by this view manager.
     */
    public function getTemplateParser() {
        return $this->templateParser;
    }

}
