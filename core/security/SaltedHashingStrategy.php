<?php

namespace esprit\core\security;

/**
 * A password hashing class that uses PHP's "hash()" function with a salt. 
 *
 * @author jbowens
 */
class SaltedHashingStrategy implements PasswordHashingStrategy
{

    protected $salt;
    protected $algorithm;

    public function __construct( $algorithm, $salt )
    {
        $this->salt = $salt;
        $this->algorithm = $algorithm;
    }

    /**
     * Returns the hash of a given piece of data.
     *
     * @param $password  the piece of data to hash
     * @return the hash of $password
     */
    public function hash( $password )
    {
        return hash( $this->algorithm, $this->combineSalt( $password ) );
    }

    /**
     * Returns true if the password matches the expected hash.
     * 
     * @param $password  a password to test
     * @param $passwordHash  the hash to test the password against
     * @return true iff the password matches the expected hash
     */
    public function matchesHash( $password, $passwordHash )
    {
        return $this->hash( $password ) == $passwordHash; 
    }

    /**
     * Takes a piece of data and applies the salt. If you override this method you must
     * also override removeSalt()
     *
     * @param $data  the piece of data to apply the salt to
     * @return the salted piece of data
     */
    protected function combineSalt( $data )
    {
        return $this->salt . $data;
    }

    /**
     * Takes a salted piece of data and removes the salt. If you override this method you 
     * must also override combineSalt()
     *
     * @param $saltedData  the salted data to unsalt
     * @return the original, unsalted piece of data
     */
    protected function removeSalt( $saltedData )
    {   
        return substr( $saltedData, strlen($this->salt) );
    }

}
