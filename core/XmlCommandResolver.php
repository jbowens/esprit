<?php

package esprit\core;

/**
 * A command resolver that uses an xml file to map requests
 * to commands.
 *
 * @jbowens
 */
class XmlCommandResolver {

    /**
     * Constructs a new XmlCommandResolver from an XML file outlining how
     * to resolve requests.
     */
    public function __construct( $xmlFilename ) {

       // TODO: Stuff 

    }

    /**
     * See CommandResolver::resolve(Request $req)
     */
    public function resolve(Request $req) {

        // TODO: Stuff

        return null;
    }

}
