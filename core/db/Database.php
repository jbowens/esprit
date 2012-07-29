<?php

namespace esprit\core\db;

use \PDO as PDO;
use \esprit\core\util\Logger as Logger;
use \esprit\core\exceptions\NonexistentDatabaseException;

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
     * @param Logger $logger
	 * @param array $driver_options
	 */
	public function __construct($dsn, $username, $password, Logger $logger, $driver_options = array()) {
	
        $this->logger = $logger;
		$this->dbh = new PDO($dsn, $username, $password, $driver_options);

	}

    public function beginTransaction() {
        $this->checkConnection();
        return $this->dbh->beginTransaction();
    }

    public function commit() {
        $this->checkConnection();
        return $this->dbh->commit();
    }

    public function errorCode() {
        $this->checkConnection();
        return $this->dbh->errorCode();
    }

    public function errorInfo() {
        $this->checkConnection();
        return $this->dbh->errorInfo();
    }

    public function exec($statement) {
        $this->checkConnection();
        return $this->dbh->exec($statement);
    }

    public function getAttribute( $attribute ) {
        $this->checkConnection();
        return $this->dbh->getAttribute( $attribute );
    }

    public static function getAvailableDrivers() {
        return PDO::getAvailableDrivers();
    }

    public function inTransaction() {
        $this->checkConnection();
        return $this->dbh->inTransaction();
    }

    public function lastInsertId($name = null) {
        $this->checkConnection();
        return $this->dbh->lastInsertId();
    }

    public function prepare($statement, $driver_options = array()) {
        $this->checkConnection();
        $stmt = $this->dbh->prepare($statement, $driver_options);
        if( $stmt === false )
            return $stmt;
        else
            return new Statement( $stmt, $this->logger );
    }

    public function query($statement) {
        $this->checkConnection();
        $stmt = $this->dbh->query($statement);
        if( $stmt === false )
            return $stmt;
        else
            return new Statement( $stmt, $this->logger );
    }
	
    public function quote($string, $parameter_type = PDO::PARAM_STR) {
        $this->checkConnection();
        return $this->dbh->quote($string, $parameter_type);
    }

    public function rollBack() {
        $this->checkConnection();
        return $this->dbh->rollBack();
    }

    public function setAttribute($attribute, $value) {
        $this->checkConnection();
        return $this->dbh->setAttribute($attribute, $value);
    }

    /**
     * Checks to ensure we're still connected to the database.
     */
    public function checkConnection() {
        if( $this->dbh == null )
            throw new NonexistentDatabaseException("That database connection was closed.");
    }

    /**
     * Closes the database connection
     */
    public function close() {
        // Destroy the reference to the PDO object to close the connection
        $this->dbh = null;
    }

}
