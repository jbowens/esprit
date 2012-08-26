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
    use LogAware;

    protected $logger;
    protected $config;

    protected $response;
    protected $otherVariables;

    /**
     * Default constructor for a template parser.
     */
    public function __construct(Config $config, Logger $logger) {
        $this->logger = $logger;
        $this->config = $config;
        $this->otherVariables = array();
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
     * Takes a template and returns the name of the template resource
     * that it uses. For example, if you store templates on the filesystem,
     * this method should return the filename of the given template.
     */
    public abstract function getResourceName( $template );

    /**
     * Sets a variable in the templating engine. Always use string keys for variables.
     * Integer keys can cause undefined behavior when merged with the values retrieved
     * from the Response object.
     *
     * @param string $key  the key of the variable
     * @param $val  the value to save in the variable
     *
     * @throws InvalidArgumentException when given a non-string variable name
     */
    public function setVariable( $key, $val ) {
        if( ! is_string($key) ) {
            $this->error("Received non-string variable name, \"" . $key . "\"");
            throw new \InvalidArgumentException("Received nonstring variable name");
        }
        $this->otherVariables[$key] = $val;
    }

    /**
     * Gets a variable as it is set in the template parser scope.
     *
     * @param $variableName  the variable name to look up
     * @return the value of the given variable in the template parser scope
     */
    public function getVariable( $key ) {
        if( ! is_string($key) ) {
            $this->error("Received non-string variable name, \"".$key."\"");
            throw new \InvalidArgumentException("Received nonstring variable name");
            // TODO: Maybe not throw an exception and instead return null?
            // We're already logging the error, so it might be better to gracefully fail.
        }
        if( array_key_exists( $key, $this->otherVariables ) )
        {
            return $this->otherVariables[$key];
        }
        if( $this->response != null && $this->response->keyExists($key) )
        {
            return $this->response->get($key);
        }
        return null;
    }

    /**
     * Sets the response object the parser should use. The response object should be
     * used to populate the tempalte parser's variables. It would be a good idea
     * to override this to load the response variables into the template parser.
     */
    public function loadResponse( Response $response ) {
        $this->response = $response;
    }

    /**
     * Returns an associative array of the variables that should be defined within
     * the templating scope.
     */
    public function getVariables() {
        // Because $this->otherVariables appears second, if a key appears in both arrays
        // the value in $this->otherVariables will be taken.
        return array_merge($this->response->getAsArray(), $this->otherVariables);
    }
}
