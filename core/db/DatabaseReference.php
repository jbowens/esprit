<?php

namespace esprit\core\db;

/**
 * A reference to a database. This is a convenient way of passing a reference to a database
 * without forcing the DatabaseManager to actually initiate a connection yet. Classes may
 * pass DatabaseReferences freely, without regard of whether they are actually going to need
 * the active database connection during this request.
 *
 * @since January 5 2013
 * @author jbowens
 */
class DatabaseReference
{

    /**
     * The database manager to extract the database from.
     */
    protected $dbm;

    /**
     * The handle of the database referenced.
     */
    protected $dbhandle;

    /**
     * Constructs a new DatabaseReference
     * 
     * @param DatabaseManager $dbm  the dbm containing the database
     * @param string $handle  the handle of the database to reference
     */
    public function __construct( DatabaseManager $dbm, $handle = 'default' )
    {
        // Ensure that the database handle actually exists.
        if( ! $dbm->handleExists($handle) )
            throw new \InvalidArgumentException("The given database manager contains no such database handle: '".$handle."'"); 

        $this->dbm = $dbm;
        $this->dbhandle = $handle;
    }

    /**
     * Dereference this database reference, returning an actual Database object that
     * may be queried.
     *
     * @return the referenced Database object
     */
    public function deref()
    {
        return $this->dbm->getDb($this->dbhandle);
    }

    /**
     * Returns the database manager used for this reference
     */
    public function getDatabaseManager()
    {
        return $this->dbm;
    }

    /**
     * Returns the database handle of the reference.
     */
    public function getHandle()
    {
        return $this->dbhandle;
    }

}
