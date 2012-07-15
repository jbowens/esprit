<?php

namespace esprit\core;

use esprit\Core\util\Logger as Logger;

/**
 * An abstract class for a template parser. This allows you to use alternative 
 * template parsers by subclassing this class and wrapping the other templating
 * engine.
 *
 * @author jbowens
 */
abstract class TemplateParser {

    protected $logger;
    protected $config;

    protected $response;

    /**
     * Default constructor for a template parser.
     */
    public function __construct(Config $config, Logger $logger) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     *  Determines if the given template exists. The argument should
     *  not be the actual filename of the template but a unique identifier
     *  that may be translated into a filename.
     *
     *  @param String $template  the template identifier of the template to check
     */
    public abstract function templateExists( $template );

    /**
     * Displays the given template.
     *
     * @param string $template  the template identifier of the template to display
     */
    public abstract function displayTemplate( $template );

    /**
     * Sets the response object the parser should use. The response object should be
     * used to populate the tempalte parser's variables. It would be a good idea
     * to override this to load the response variables into the template parser.
     */
    public function loadResponse( Response $response ) {
        $this->response = $response;
    }

    public function getVariables() {
        return $this->response->getAsArray();
    }
}
