<?php

namespace esprit;

require_once "core/Controller.php";

/**
 * This file adds an autoloader for all default esprit classes.
 */

function autoload( $class ) {
    $classPieces = explode("\\", $class);

    if( count($classPieces) == 0 )
        return false;

    if( $classPieces[0] != 'esprit' )
        return false;

    unset($classPieces[0]);

    $file = implode('/', $classPieces).'.php';

    if( @file_exists( __DIR__ . DIRECTORY_SEPARATOR . $file ) )
    {
        require_once( __DIR__ . DIRECTORY_SEPARATOR . $file );
        return true;
    }
}


spl_autoload_register(__NAMESPACE__.'\autoload', true);

