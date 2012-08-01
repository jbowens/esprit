<?php

namespace esprit\core\security;

/**
 * An interface for methods for hashing passwords.
 *
 * @author jbowens
 */
interface PasswordHashingStrategy {

    public function hash( $password );

    public function matchesHash( $password, $passwordHash );

}


