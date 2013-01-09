<?php

namespace esprit\core;

/**
 * A trait for allowing arbitrary flags on an object.
 *
 * @since 2013-01-08
 * @author jbowens
 */
trait Flaggable
{

    // An array of currently defined flags
    protected $flags = array();

    /**
     * Determines if the given flag is raised.
     */
    public function hasFlag( $flagKey )
    {
        if( $this->isFlagDefined( $flagKey ) )
            return $this->flags[$flagKey];
        else
            return false;
    }

    /**
     * Determines if the given flag is defined.
     */
    public function isFlagDefined( $flagKey )
    {
        return isset( $this->flags[$flagKey] );
    }

    /**
     * Sets a flag.
     */
    public function setFlag( $flagKey, $flagValue )
    {
        $this->flags[$flagKey] = $flagValue;
    }

}
