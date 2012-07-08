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
     * Print the presentation of the output to stdout.
     *
     * @param Output $output  the output to display
     */
    public function display( Output $output ); 

}
