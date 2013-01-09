<?php

namespace esprit\core;

/**
 * An interface for classes that can annotate requests with flags.
 *
 * @since January 8, 2013
 * @author jbowens
 */
interface RequestFlagger
{

    /**
     * Flags the request, if necessary.
     *
     * @param Request $request  the request to possibly flag
     */
    public function processRequest(Request $request);

}
