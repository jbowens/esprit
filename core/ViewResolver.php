<?php

namespace esprit\core;

/**
 * Defines an interface for classes that can find and instantiate a view that should be used
 * for presenting the given Response object. This is the view layer equivalent of a CommandResolver.
 *
 * @author jbowens
 */
interface ViewResolver {

    /**
     * Takes an Response object that needs to be displayed, and returns the View instance that should
     * be used for its presentation. If no appropriate View is found, then null may be returned.
     *
     * @param Response $response  the response instance representing data calculated by the model layer
     * @return View  the view that should be used to present the output, or, null if no such view
      *              was found by the resolver.
     */
    public function resolve(Response $response);

}
