<?php

namespace esprit\core\db;

/**
 * 
 * The DatabaseManager handles all connections to databases. Multiple connections
 * may be maintained through the manager. The manager has a default connection that
 * is used when a handle is not given. The DatabaseManager uses PDO, so any database
 * software that has a PHP/PDO driver may be used.
 * 
 * @author jbowens
 *
 */
class DatabaseManager {

	/* A map of handles to database connections */
	protected $databaseConnections = array();
	
	/* The default username */
	protected $defaultUsername;
	
	/* The default password */
	protected $defaultPass;
	
	/**
	 * 
	 * Creates a new database manager. This requres a default DSN and corresponding
	 * default user and password. 
	 * 
	 * @param string $defaultDsn  the dsn of the default database
	 * @param string $defaultUser  the default database username
	 * @param string $defaultPass  the default database password
	 */
	public function __construct($defaultDsn, $defaultUser, $defaultPass) {
		$this->defaultUser = $defaultUser;
		$this->defaultPass = $defaultPass;
		$this->connectToDatabase("default", $defaultDsn, $defaultUser, $defaultPass) ;
	}
	
	/**
	 * 
	 * Returns the datbase connection associated with the given handle.
	 * 
	 * @param string $handle  the handle of the database connection
	 * @throws exceptions\NonexistentDatabaseException if no such handle exists
	 * 
	 * @return  a database connection (a PDO object)
	 */
	public function getDb($handle) {
		if( ! isset( $this->datbaseConnections[$handle] ) )
			throw new \esprit\core\exceptions\NonexistentDatabaseException();
		else
			return $this->databaseConnections[$handle];
	}
	
	/**
	 * Connects to another database, given a handle to refer to the connection by,
	 * a dsn, and optionally a username and password pair. If the username and password
	 * are omitted, the default username and password provided on construction will be used.
	 * 
	 * @param string $handle  the handle to refer to the connection in the future
	 * @param string $dsn  the dsn of the database
	 * @param string $user  (optional) the database username
	 * @param string $pass  (optional) the datbaase password
	 */
	public function connectToDatabase($handle, $dsn, $user = null, $pass = null) {
		
		if( $user == null )
			$user = $this->defaultUsername;
		
		if( $pass == null )
			$pass = $this->defaultPass;
			
		$this->databaseConnections[$handle] = new PDO($handle, $dsn, $user, $pass, array());
		
	}
	
}

