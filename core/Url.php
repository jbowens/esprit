<?php

namespace esprit\core;

/**
 * Represents a URL. This class contains many utility methods that
 * can be used to manipulate, create and grab data from urls.
 *
 * @author jbowens
 */
class Url {
	
    /* The host/domain of this url */
    protected $host;

    /* The path of this url */
    protected $path;

    /* The query string of this url */
    protected $queryString;

    /* The fragment identifier (hash) of the url */
    protected $fragmentIdentifier;

    /* The individual pieces of the path */
    protected $pathPieces;

    /**
     * Constructs a Url object from the host, path and query string.
     * This is less likely to be called directly. It's more likely that the user
     * will call Url::createUrlFromString().
     *
     * @param string $host  the host or domain of the url
     * @param string $path  the path on the file system
     * @param string $queryString  the GET query string
     * @param string $fragmentIdentifier  the fragment identifier of the url
     */
    public function __construct($host, $path, $queryString, $fragmentIdentifier) {
        $this->host = $host;
        $this->path = $path;
        $this->queryString = $queryString;
        $this->fragmentIdentifier = $fragmentIdentifier;
        $this->pathPieces = self::createPiecesFromPath( $this->path );
    }

    /**
     * Returns the host/domain of this url.
     *
     * @return string the host the url
     */
    public function getHost() {
        return $this->host;
    }
	
    /**
     * Returns the path of this url.
     *
     * @return string the path of the url
     */
     public function getPath() {
        return $this->path;
     }

    /*
     * Returns the query string of this url.
     *
     * @return string the query string of the url
     */
    public function getQueryString() {
        return $this->queryString;
    }

    /**
     * Returns the fragment identifier / hash of the url.
     *
     * @return string the fragment identifier of the url
     */
    public function getFragmentIdentifier() {
        return $this->fragmentIdentifier;
    }

    /*
     * Returns the absolute url string representation of this url.
     *
     * @return string the absoute url representation of this url
     */
    public function getAbsoluteUrl( $www = false ) {
        return 'http://' . ( $www ? : 'www.' : '' ) . 
               $this->host . 
               $this->path . 
               $this->queryString . 
               '#' . ( ($this->fragmentIdentifer != null && $this->fragmentIdentifier != '') ? '#' . $this->fragmentIdentifier : '' );
    }

    /**
     * Returns the absolute path of the given url.
     *
     * @return the absolute path of this url
     */
    public function getAbsolutePath() {
        return $this->path .
               $this->queryString .
               '#' . ( ( $this->fragmentIdentifier != null && $this->fragmentIdentifier != '') ? '#' . $this->fragmentIdentifier : '' );

    }

    /**
     * Calculates the number of 'items' (or directories/files) in the path.
     * For example /site/about.html has two 'items.'
     *
     * @return int  the number of items in the path
     */ 
    public function getPathLength() {
        return count( $this->pathPieces );
    }

    /**
     * Get the (n-1)th path piece
     *
     * @param int $index  the index of the path piece
     * @throws IndexOutOfBoundsException
     * @throws NonexistentKeyException
     * @return string  the ($index-1)th path piece
     */
    public function getPathPiece( $index ) {
        if( $index < 0 || $index >= $this->getPathLength() )
            throw new IndexOutOfBoundsException($index, $this->getPathLength());

        if( ! isset( $this->pathPieces[$index] ) )
            throw new NonexistentKeyException( $index );

        return $this->pathPieces[$index];
    }

    /**
     * Creates the url pieces from a given path (splitting on directory
     * separators)
     *
     * @param string $path  the path to split
     * @return array  an array of individual directories of the path
     */
    protected static function createPiecesFromPath( $path ) {
        return explode( DIRECTORY_SEPARATOR, $path );
    }

}
