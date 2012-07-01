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

    const SQL_GET_TRANSLATION_STRING_BY_IDENTIFER = "SELECT `translation` FROM `translations` WHERE `languageid` = ? AND `translationIdentifier` = ?";

    /* The cache key prefix to use when caching */
    protected $cacheKeyPrefix;

    /* The database to lookup translation data in */
    protected $db;

    /* Cache of localized strings */
    protected $translationCache;

    protected $languageSource;

    /**
     * Creates a new TranslationManager given a cache and a key prefix to prepend
     * on all data saved in the cache.
     */
    public function __construct( Database $db, Cache $cache, LanguageSource $langSource, $keyPrefix = 'tm_' ) {
        $this->db = $db;
        $this->translationCache = $cache;
        $this->languageSource = $langSource;
        $this->cacheKeyPrefix = $keyPrefix;
    }

     /**
     * Retrieves the given text localized to the specified lanuage.
     *
     * @param string $translationIdentifier  the translation id of the text
     * @param string $language  the language identifier
     * @throws InvalidTranslationIdentifier when the identifier doesn't exist
     */
    public function getTranslation($translationIdentifier, $language) {

        $ancestors = $this->getAncestors($language);

        for( $i = 0; $i < count($ancestors); $i++ )
        {
            $localizedString = $this->getLocalized($translationIdentifier, $ancestors[$i]);
            if( $localizedString != null )
                return $localizedString;
        }

        // The string doesn't exist
        throw new InvalidTranslationIdentifier($translationIdentifier);

    }

    public function getLocalized($translationIdentifier, Language $language) {

        $cacheKey = 'translation_' . $language->getLanguageId() . '_' . $translationIdentifier;
        if( $this->cache->isCached( $cacheKey ) )
        {
            return $this->cache->get( $cacheKey );
        }

        // Get from database
        $stmt = $this->db->prepare( self::SQL_GET_TRANSLATION_STRING_BY_IDENTIFER );
        $stmt->execute->array( $language->getLanguageId(), $translationIdentifier );

        $localizedString = $stmt->fetchColumn();
        $localizedString = $localizedString === false ? null : $localizedString;
        // Cache this localized string
        $this->cache->set($cacheKey, $localizedString);
        
        return $localizedString;
    }

    /**
     * Returns an array of the ancestor of a language in order, starting
     * at (and including) the language itself, continuning until the tree root.
     */
    protected function getAncestors($languageIdentifier) {

        $ancestors = array();

        $language = $langSource->getLanguageByIdentifier( $languageIdentifier );
        array_push( $ancestors, $language );

        if( $language->getParentId() != null ) {
         
            $stmt = $this->db->prepare( LanguageSource::SQL_LANG_BY_ID );

            while( $language->getParentId() != null )
            {
                $stmt->execute( array( $language->getParentId() ) );
                $arr = $stmt->fetch(PDO::FETCH_ASSOC);
                $language = Language::createFromArray($arr);
                array_push( $ancestors, $language );
            }
        }

        return $ancestors;
    }

}
