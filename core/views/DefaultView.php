<?php

namespace esprit\core\views;

use \esprit\core\AbstractView as AbstractView;
use \esprit\core\Response as Response;

/**
 * This is a default view that most likely most requests will use. It searches the template
 * system for a template that matches the pathname of the requested url. If it finds it,
 * it prints that template, embedded inside the existing DefaultTemplate template.
 *
 * @author jbowens
 */
class DefaultView extends AbstractView {

    const DEFAULT_TEMPLATE = "Default";
    const LOG_SOURCE = "DefaultView";

    public function generateOutput( Response $response )
    {
        $this->templateParser->loadResponse( $response );

        /* Find the desired inner template based on the request path  */
        $url = $response->getRequest()->getUrl();
        $path = $url->getPath();

        $innerTemplate = null;
        if( $path == "" || $path == "/" || $url->getPathPiece(0) == "" ) {
            $innerTemplate = 'Index';
        }
        else {
            // Clean up the path pieces into class pieces
            $classPieces = array();
            for( $i = 0; $i < $url->getPathLength(); $i++ ) {
                $innerPieces = explode('-', $url->getPathPiece($i));
                $innerPieces = array_map('ucfirst', $innerPieces);
                array_push($classPieces, implode('', $innerPieces));
            }

            for( $i = count($classPieces)-1; $i >= 0; $i-- ) {
                
                $testPieces = array();
                for( $j = $i; $j >= 0; $j-- )
                    array_push($testPieces, $classPieces[$j]);

                $potentialTemp = implode('_', $testPieces);

                if( $this->templateParser->templateExists( $potentialTemp ) ) {
                    $innerTemplate = $potentialTemp;
                    break;
                }
            }
        }

        // Log an error if we didn't find an appropriate template
        if( $innerTemplate == null )
            $this->logger->error("Could not find a default template for request with path " . $path, self::LOG_SOURCE);

        // Pass the inner template on to the default template to include
        $this->templateParser->setVariable( 'DefaultView_innerTemplate', $this->templateParser->getResourceName( $innerTemplate ) );

        // Display it
        $this->templateParser->displayTemplate( self::DEFAULT_TEMPLATE );
    }

}
