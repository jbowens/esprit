<?php

namespace esprit\core;

/**
 * An immutable class that represents an email address.
 */
final class EmailAddress
{

    protected $displayName;
    protected $address;

    /**
     * Constructs an EmailAddress object from a string email address.
     *
     * @param $address  an email address in someone@example.com format.
     */
    public static function createFromAddress( $address )
    {
        return new EmailAddress( $address, null );
    }


    /**
     * @param $address  an email address in someone@example.com format
     * @param $name  a name associated with the email address.
     */
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
        $matches = array();
        preg_match('/.*@(.+)/', $this->address, $matches);
        return $matches[1];
    }


}
