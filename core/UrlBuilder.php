<?php

namespace esprit\core;

/**
 * A utility class that assists in the construction of Url objects.
 *
 * @author jbowens
 */
class UrlBuilder {

    /* The configuration object to use for installation-specific values */
    protected $config;

    /**
     * Constructs a new UrlBuilder from a Configuration object.
     *
     * @param Config $configuration  the config object to use
     */
    public function __construct( Config $configuration ) {
        $this->config = $configuration;
    }

    /**
	 * Constructs a new Url object from a given url string.
	 * 
	 * @param string $urlString
     * @throws MalformedUrlException if the given url is malformed
     * @return a Url formed from the given string
	 */
	public function createUrlFromString($urlString) {
        
        // Regardless of what time of url it is you might have a query string or hash
        $queryString = self::extractQueryString( $urlString );
        $fragmentIdentifier = self::extractFragmentIdentifier( $urlString );

		if( substr($urlString, 0, 7) === 'http://' ) {
            // Absolute url
            preg_match('/^http://(www.)?([^/?]*)(/[^?]*)?/i', $urlString, $matches);
            $host = isset($matches[2]) ? $matches[2] : '';
            $path = isset($matches[3]) ? $matches[3] : '';
        } else if( substr($urlString, 0 , 1) === '/' ) {
            // Absolute path
            $host = $this->config->get('host');
            $path = substr($urlString, 0, strlen($urlString) - strlen($queryString));
        } else {
            // Relative paths are not supported
            throw new MalformedUrlException("Unsupported path format received");
        }

        return new Url($host, $path, $queryString, $fragmentIdentifier);
	}

    /**
     * Returns the query string, if any, for a given url string
     * with a query string. The given url does not need to be a full
     * url. It only needs to contain a query string.
     *
     * @param string $urlString  a full or partial url string
     * @return string  query string of the given url snippet.
     */
    public static function extractQueryString( $urlString ) {
        $urlPieces = explode('?', $urlString);
        if( count($urlPieces) == 1 )
            return '';
        else {
            return '?'.$urlPieces[1];
        }
    }

    /**
     * Returns the hash/fragment identifier of a url.
     *
     * @param string $urlString  a full or partial url
     * @return the fragment identifier of the url
     */
     public static function extractFragmentIdentifier( $urlString ) {
        $urlPieces = explode('#', $urlString);
        if( count($urlPieces) == 1 )
            return '';
        else
            return $urlPieces[1];
     }

}
