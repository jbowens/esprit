<?php

namespace esprit\core\exceptions;

/**
 * An exception thrown when there's an error connecting to a database.
 * This is implemented as a wrapper for the PDOException.
 *
 * @author jbowens
 */
class DatabaseConnectionException extends Exception {

    protected $pdoException;

    public function __construct( PDOException $exception ) {
        $this->pdoException = $exception;
    }

    public function getMessage() {
        return "[DB] " . $this->pdoException->getMessage();
    }

    public function getErrorInfo() {
        return $this->pdoException->errorInfo;
    }

    public function getSqlErrorCode() {
        return $this->pdoException->getCode();
    }

}
