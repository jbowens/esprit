<?php

namespace esprit\core\db;

use \esprit\core\util\Logger as Logger;
use \PDOException as PDOException;

/**
 * The DatabaseManager handles all connections to databases. Multiple connections
 * may be maintained through the manager. The manager has a default connection that
 * is used when a handle is not given. The DatabaseManager uses PDO, so any database
 * software that has a PHP/PDO driver may be used.
 * 
 * @author jbowens
 */
class DatabaseManager {

    /* A map of handles to database connections */
    protected $databaseConnections = array();

    /* A map of lazy database connections. These connections have not been made yet,
     * but will be created if needed. */
    protected $lazyDatabaseConnections = array();

    /* The default username */
    protected $defaultUsername;
    
    /* The default password */
    protected $defaultPass;

    protected $logger;
    
    /**
     * Creates a new database manager. This requres a default DSN and corresponding
     * default user and password. 
     * 
     * @param string $defaultDsn  the dsn of the default database
     * @param string $defaultUser  the default database username
     * @param string $defaultPass  the default database password
     * @param Logger $logger  the logger to use for db operations
     */
    public function __construct($defaultDsn, $defaultUser, $defaultPass, $logger) {
        $this->defaultUser = $defaultUser;
        $this->defaultPass = $defaultPass;
        $this->logger = $logger;
        $this->lazyConnectToDatabase("default", $defaultDsn, $defaultUser, $defaultPass) ;
    }
    
    /**
     * Returns the datbase connection associated with the given handle.
     * 
     * @param string $handle  the handle of the database connection
     * @throws exceptions\NonexistentDatabaseException if no such handle exists
     * 
     * @return  a database connection (a PDO object)
     */
    public function getDb($handle = "default") {
        if( ! isset( $this->databaseConnections[$handle] ) )
        {
            // Is this an uninitialized lazy connection?
            if( isset( $this->lazyDatabaseConnections[$handle] ) )
            {
                $connInfo = $this->lazyDatabaseConnections[$handle];
                unset($this->lazyDatabaseConnections[$handle]);
                $this->connectToDatabase($handle,
                                         $connInfo['dsn'],
                                         $connInfo['user'],
                                         $connInfo['pass']);
                return $this->databaseConnections[$handle];
            } else
            {
                throw new \esprit\core\exceptions\NonexistentDatabaseException("The database '".$handle."' does not exist.");
            }
        }
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
     *
     * @throws DatabaseConnectionException  if the connection attempt fails
     */
    public function connectToDatabase($handle, $dsn, $user = null, $pass = null) {
        
        if( $user == null )
            $user = $this->defaultUsername;
        
        if( $pass == null )
            $pass = $this->defaultPass;
        
        try {   
            $this->databaseConnections[$handle] = new Database($dsn, $user, $pass, $this->logger, array());

            $this->logger->info("Connected to " . $dsn . " as " .$user . " under handle " . $handle, "DATABASE");
        
        } catch(PDOException $ex) {
            $this->logger->severe("Error connecting to " . $dsn, "DATABASE", array( 'handle' => $handle,
                                                                                    'dsn'    => $dsn,
                                                                                    'user'   => $user ));
            throw new \esprit\core\exceptions\DatabaseConnectionException( $ex );
        }
    }

    /**
     * Records information to connect to the database. If the associated handle is ever used,
     * a connection will be opened to the database for the rest of the dbm's lifetime or until
     * it's explicitly closed. This method has the advantage of not connecting to the database
     * unless the connection is actually used.
     *
     * @param string $handle  the handle to refer to the connection in the future
     * @param string $dsn  the dsn of the database
     * @param string $user  (optional) the database username
     * @param string $pass  (optional) the database password
     */
    public function lazyConnectToDatabase($handle, $dsn, $user = null, $pass = null) {
        
        if( $user == null )
            $user = $this->defaultUsername;

        if( $pass == null )
            $pass = $this->defaultPass;

        $this->lazyDatabaseConnections[$handle] = array(
                                                    "dsn" => $dsn,
                                                    "user" => $user,
                                                    "pass" => $pass
                                                  );    

    }

    /**
     * Determines if there is an *active* connection with the given handle. This will return false for
     * lazy connections.
     *
     * @param string $handle  the handle to check
     */
    public function connectionExists($handle) {
        return isset($this->databaseConnections[$handle]) && $this->databaseConnections[$handle]->checkConnection();
    }

    /**
     * Determines if the given handle exists in the manager. A true value does not necessarily mean the
     * connection is active or that the handle has a valid dsn/user/pass combination--only that the handle
     * was set.
     *
     * @param string $handle  the handle to check
     */
    public function handleExists($handle) {
        return isset($this->databaseConnections[$handle]) || isset($this->lazyDatabaseConnections[$handle]);
    }

    /**
     * Closes a database connection.
     *
     * @param string $handle  the handle referring to the connection
     */
    public function closeConnection($handle) {
        
        if( ! isset( $this->databaseConnections[$handle] ) && ! isset($this->lazyDatabaseConnections[$handle]) )
            throw new NonexistentDatabaseException("No database with the handle " . $handle . " exists.");
        
        if( isset( $this->databaseConnections[$handle] ) )
        {
            $this->databaseConnections[$handle]->close();
            unset($this->databaseConnctions[$handle]);
        }

        if( isset( $this->lazyDatabaseConnections[$handle] ) )
        {
            unset($this->lazyDatabaseConnections[$handle]);
        }

    }

    /**
     * Releases all system resources being used by the database manager, including
     * including closing all current database connections managed by the manager.
     */
    public function close() {
        // Close active connections
        foreach( $this->databaseConnections as $handle => $db ) {
            $this->closeConnection($handle);
        }

        // Remove uninitialized lazy connections
        foreach( $this->lazyDatabaseConnections as $handle => $connInfo ) {
            $this->closeConnection($handle);
        }
    }

}

