<?php

namespace esprit\core;

/**
 * An immutable class that represents an email address.
 */
final class EmailAddress
{

    protected $displayName;
    protected $address;

    public static function createFromAddress( $address )
    {
        return new EmailAddress( $address, null );
    }


    public function __construct( $address, $name )
    {
        $this->address = $address;
        $this->displayName = $name;
    }

    /**
     * Returns the address formatted, ready for the TO section of an
     * email. The returned address is guaranteed to be in RFC 2822 format.
     */
    public function getFormattedAddress()
    {
        if( $this->displayName )
        {
            return $this->displayName . " <" . $this->address . ">";
        }
        else
            return $this->address;
    }

    /**
     * Returns the host name attached to this email address.
     */
    public function getHostname()
    {
        // TODO: Test this method
        $matches = array();
        preg_match('/.*@(.+)/', $this->address, $matches);
        return $matches[1];
    }


}
