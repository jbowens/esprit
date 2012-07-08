<?php

namespace esprit\core;

/**
 * A TemplateParser that uses the Twig templating engine for its
 * backend.
 *
 * @author jbowens
 */
class TwigTemplateParser extends TemplateParser {

    public function __construct(Logger $logger, Config $config) {
        parent::__construct($logger, $config);
        //TODO: Check for twig config options
        //TODO: Setup Twig
    }

    public function templateExists( $template ) {
        //TODO: Implement
    }

    public function displayTemplate( $template ) {
        //TODO: Implement
    }

}
