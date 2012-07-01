<?php

namespace esprit\core\db;

/**
 * A wrapper for PDOStatements. Allows for additional functionality
 * to be added into database queries.
 * 
 * @author jbowens
 */
class Statement {
	
    protected $stmt;

	function __construct(PDOStatement $originalStatement) {
		$this->stmt = $originalStatement;
	}

    // Forward all method calls to the $stmt
    public function __call($method, $params) {
        return call_user_func_array( array($this->stmt, $method), $params );
    }

}

