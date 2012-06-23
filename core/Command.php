<?php

namespace esprit\core;

/**
 * A command is the primary source of business logic for a request. Every
 * request is mapped to exactly one command, dependent on the configuration
 * of the system. 
 */
interface Command {

    /**
     * Responds to the given page request, producing a Response.
     *
     * @return Response  the response
     */
    public function execute(Request $request, Response $response);

    /**
     * Gets the name of this command.
     */
    public function getName();

}
