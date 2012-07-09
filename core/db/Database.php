<?php

namespace esprit\core\db;

use \PDO as PDO;
use \esprit\core\util\Logger as Logger;

/**
 * A database wrapper for PDO. Allows for more functionality to be built onto PDO
 * 
 * @author jbowens
 */
class Database {

    protected $dbh;

    protected $logger;

	/**
	 * Default constructor for a datbase. Constructs from a DSN.
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $driver_options
	 */
	public function __construct($dsn, $username, $password, $logger, $driver_options = array()) {
	
        $this->logger = $logger;
		$this->dbh = new PDO($dsn, $username, $password, $driver_options);

	}

    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }

    public function commit() {
        return $this->dbh->commit();
    }

    public function errorCode() {
        return $this->dbh->errorCode();
    }

    public function errorInfo() {
        return $this->dbh->errorInfo();
    }

    public function exec($statement) {
        return $this->dbh->exec($statement);
    }

    public function getAttribute( $attribute ) {
        return $this->dbh->getAttribute( $attribute );
    }

    public static function getAvailableDrivers() {
        return PDO::getAvailableDrivers();
    }

    public function inTransaction() {
        return $this->dbh->inTransaction();
    }

    public function lastInsertId($name = null) {
        return $this->dbh->lastInsertId();
    }

    public function prepare($statement, $driver_options = array()) {
        $stmt = $this->dbh->prepare($statement, $driver_options);
        if( $stmt === false )
            return $stmt;
        else
            return new Statement( $stmt, $logger );
    }

    public function query($statement) {
        $stmt = $this->dbh->query($statement);
        if( $stmt === false )
            return $stmt;
        else
            return new Statement( $stmt, $logger );
    }
	
    public function quote($string, $parameter_type = PDO::PARAM_STR) {
        return $this->dbh->quote($string, $parameter_type);
    }

    public function rollBack() {
        return $this->dbh->rollBack();
    }

    public function setAttribute($attribute, $value) {
        return $this->dbh->setAttribute($attribute, $value);
    }

}
