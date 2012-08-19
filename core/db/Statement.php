<?php

namespace esprit\core\db;

use \esprit\core\util\Logger as Logger;

/**
 * A wrapper for PDOStatements. Allows for additional functionality
 * to be added into database queries.
 * 
 * @author jbowens
 */
class Statement {

    const LOG_ORIGIN = 'DATABASE';
    const PDO_ERRORINFO_MESSAGE_INDEX = 2;

    protected $stmt;
    protected $logger;

	function __construct(\PDOStatement $originalStatement, Logger $logger) {
		$this->stmt = $originalStatement;
        $this->logger = $logger;
	}

    /**
     * execute wrapper with logging
     */ 
    public function execute( $input_parameters = array() ) {
        $this->logger->fine( 'Executing query: ' . $this->stmt->queryString, self::LOG_ORIGIN, $input_parameters );
        $result = $this->stmt->execute( $input_parameters );

        // Check for an error
        $errorCode = $this->stmt->errorCode();
        if( $errorCode != "00000" )
        {
            $this->logger->error( 'SQL error ('.$errorCode.'): ' . $this->errorInfo()[self::PDO_ERRORINFO_MESSAGE_INDEX], self::LOG_ORIGIN );
        }

        return $result;
    }

    // Forward all method calls to the $stmt
    public function __call($method, $params) {
        return call_user_func_array( array($this->stmt, $method), $params );
    }

}

