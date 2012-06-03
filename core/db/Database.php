<?php

namespace \esprit\core\db;

/**
 * 
 * A database wrapper for PDO. Allows for more functionality to be built onto PDO
 * 
 * @author jbowens
 *
 */
class Database extends PDO {

	/**
	 * 
	 * Default constructor for a datbase. Constructs from a DSN.
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $driver_options
	 */
	public function __construct($dsn, $username, $password, $driver_options = array()) {
	
		$result = parent::__construct($dsn, $username, $password, $driver_options);
		
		$this->setAttribute(self::ATTR_STATEMENT_CLASS, array('DBStatement', array($this)));
		
		return $result;
	
	}
	
}

