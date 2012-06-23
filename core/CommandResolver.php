<?php

namespace esprit\core;

/**
 * An interface for objects that determine what command to run based on the
 * request
 *
 * @jbowens
 */
interface CommandResolver {

    /**
     * Determines what command to execute. Returns null if no command matches.
     *
     * @param Request $req  the request to serve
     * @return Command  the command to run
     */
    public function resolve(Request $req);

}
