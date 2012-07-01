<?php

namespace esprit\core;

/**
 * The TranslationManager is a source of localized strings. It uses
 * memcached to store localized strings in memory. The localized strings are
 * loaded into memory on bounce.
 *
 * @author jbowens
 */
class TranslationManager implements TranslationSource {

    /* The cache key prefix to use when caching */
    protected $cacheKeyPrefix;

    /* The database to lookup translation data in */
    protected $db;

    /* Cache of localized strings */
    protected $translationCache;

    /**
     * Creates a new TranslationManager given a cache and a key prefix to prepend
     * on all data saved in the cache.
     */
    public function __construct( Database $db, Cache $cache, $keyPrefix = 'tm_' ) {
        $this->translationCache = $cache;
        $this->cacheKeyPrefix = $keyPrefix;
    }

     /**
     * Retrieves the given text localized to the specified lanuage.
     *
     * @param string $translationIdentifier  the translation id of the text
     * @param string $language  the language identifier
     */
    public function getTranslation($translationIdentifier, $language) {


    }

    /**
     * Returns an array of the ancestor of a language in order, starting
     * at (and including) the language itself, continuning until the tree root.
     */
    protected function getAncestors($language) {

    }

}
