<?php

namespace esprit\core;

/**
 * The session object that stores the user's session data.
 *
 * @author jbowens
 */
class Session {

    protected $sessionid;
    protected $data;

    /**
     * Creates a new Session object by pulling data from automagically
     * PHP-populated variables.
     *
     * @return a Session object
     */
    public static function createFromEnvironment()
    {
        return new Session( session_id(),  $_SESSION );
    }

    public function __construct( $sessionid,  array $sessionData )
    {
        $this->sessionid = $sessionid;
        $this->data = $sessionData;
    }

    /**
     * Gets a value stored in the current session
     *
     * @param $key  the key to lookup
     * @return the associated session value
     */
    public function get( $key )
    {
        return $this->data[$key];
    }

    /**
     * Determines if the given key is set in the current session
     *
     * @param $key  the key to lookup
     * @return true iff the key is set in the current session
     */
    public function keyExists( $key )
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Returns the user's session id
     *
     * @return the session id of the user
     */
    public function getSessionId()
    {
        return $this->sessionid;
    }

}
