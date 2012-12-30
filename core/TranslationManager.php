<?php

namespace esprit\core;

use \esprit\core\exceptions\InvalidTranslationIdentifier as InvalidTranslationIdentifier;

/**
 * The TranslationManager is a source of localized strings. It uses
 * memcached to store localized strings in memory. The localized strings are
 * loaded into memory on bounce.
 *
 * @author jbowens
 */
class TranslationManager implements TranslationSource {

    const SQL_GET_TRANSLATION_STRING_BY_IDENTIFER = "SELECT `translation` FROM `translations` WHERE `languageid` = ? AND `translationIdentifier` = ?";

    /* The database to lookup translation data in */
    protected $databaseHandle;

    /* The dbm to access the database through */
    protected $dbm;

    /* Cache of localized strings */
    protected $translationCache;

    protected $languageSource;

    /**
     * Creates a new TranslationManager given a cache and a key prefix to prepend
     * on all data saved in the cache.
     */
    public function __construct( db\DatabaseManager $dbm, Cache $cache, LanguageSource $langSource, $keyNamespace = 'tm', $databaseHandle = 'default' ) {
        $this->dbm = $dbm;
        $this->databaseHandle = $databaseHandle;
        $this->translationCache = $cache->accessNamespace( $keyNamespace );
        $this->languageSource = $langSource;
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

        foreach( $ancestors as $ancestor )
        {
            $localizedString = $this->getLocalized($translationIdentifier, $ancestor);
            if( $localizedString != null ) {
                return $localizedString;
            }
        }

        // The string doesn't exist
        throw new InvalidTranslationIdentifier($translationIdentifier);

    }

    public function getLocalized($translationIdentifier, Language $language) {

        $cacheKey = 'translation_' . $language->getLanguageId() . '_' . $translationIdentifier;
        if( $this->translationCache->isCached( $cacheKey ) )
        {
            return $this->translationCache->get( $cacheKey );
        }

        // Get from database
        $stmt = $this->dbm->getDb($this->databaseHandle)->prepare( self::SQL_GET_TRANSLATION_STRING_BY_IDENTIFER );
        $stmt->execute(array( $language->getLanguageId(), $translationIdentifier ));

        $localizedString = $stmt->fetchColumn();
        $localizedString = $localizedString === false ? null : $localizedString;
        // Cache this localized string
        $this->translationCache->set($cacheKey, $localizedString);

        return $localizedString;
    }

    /**
     * Returns an array of the ancestor of a language in order, starting
     * at (and including) the language itself, continuning until the tree root.
     */
    public function getAncestors($languageIdentifier) {

        $ancestors = array();

        $language = $this->languageSource->getLanguageByIdentifier( $languageIdentifier );
        array_push( $ancestors, $language );

        if( $language->getParentId() != null ) {
         
            while( $language->getParentId() != null )
            {
                $language = $this->languageSource->getLanguageById( $language->getParentId() );
                array_push( $ancestors, $language );
            }
        }

        return $ancestors;
    }

}
