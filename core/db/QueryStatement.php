<?php

namespace esprit\core\db;

/**
 * 
 * A wrapper for PDOStatements. Allows for additional functionality
 * to be added into database queries.
 * 
 * @author jbowens
 *
 */
class QueryStatement extends PDOStatement {
	
	function __construct() {
		parent::__construct();
	}
}

