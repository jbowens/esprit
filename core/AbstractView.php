<?php

namespace esprit\core;

/**
 * An abstract class of View that implements some basic functionality
 * most views can benefit from. For a view to be instantiable through
 * the default view resolvers, it must extend this class so that this
 * class's constructor may be used.
 *
 * @author jbowens
 */
abstract class AbstractView implements View {

    protected $templateParser;

    public function __construct(TemplateParser $templateParser) {
        $this->templateParser = $templateParser;
    }

    /**
     * See View.display(Response $response);
     */
    public function display(Response $response) {
        $this->generateOutput( $response );
    }

    /**
     * This is the method that should actually generate the output.
     */
    protected abstract function generateOutput(Response $response);

    /**
     * Sets the HTTP status to the given status.
     *
     * @param HttpStatusCode $status  the http status to set
     */
    protected function setStatus(HttpStatusCode $status) {
        http_response_code($status->getCode());
    }

}
