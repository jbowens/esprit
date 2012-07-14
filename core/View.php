<?php

namespace esprit\core;

/**
 * An interface for implementations of the presentation layer.
 * Most views should just extend AbstractView instead of supplying
 * a complete implementation of this interface. Views must extend
 * AbstractView in order to be instantiable by the default
 * ViewResolvers.
 *
 * @author jbowens
 */
interface View {

    /**
     * Print the presentation of the response to stdout.
     *
     * @param Response $response  the response to display
     */
    public function display( Response $response ); 

}
