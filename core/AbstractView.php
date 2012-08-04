<?php

namespace esprit\core;

use \esprit\core\util\Logger as Logger;

/**
 * An abstract class of View that implements some basic functionality
 * most views can benefit from. For a view to be instantiable through
 * the default view resolvers, it must extend this class so that this
 * class's constructor may be used.
 *
 * @author jbowens
 */
abstract class AbstractView implements View {

    protected $config;
    protected $logger;
    protected $templateParser;
    protected $response;

    public function __construct(Config $config, Logger $logger, TemplateParser $templateParser) {
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
    }

    /**
     * See View.display(Response $response);
     */
    public function display(Response $response) {
        $this->response = $response;
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

    /**
     * Sets a header
     */
    protected function setHeader( $key, $value ) {
        header( $key . ': ' . $value );
    }

    /**
     * Redirects the user to the provided page.
     */
    protected function redirect($where, $permanently = false)
    {
        if( $permanently )
            $this->setStatus( new HttpStatusCodes\MovedPermanently() );
        else
            $this->setStatus( new HttpStatusCodes\TemporaryRedirect() );

        if( $where[0] == "/" && $this->response != null ) {
            $domain = $this->response->getRequest()->getSite()->getDomain();
            $absoluteUrl = "http://" . $domain . $where;
        } else
            $absoluteUrl = $where;

        $this->setHeader( 'Location', $absoluteUrl );

        exit;

    }

}
