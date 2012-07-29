<?php

namespace esprit\core;

/**
 * A class for getting languages from the database (or cache if available). This class uses
 * the flyweight pattern since Language is an immutable class.
 *
 * @author jbowens
 */
class LanguageSource {

    const SQL_LANG_BY_ID = "SELECT `languageid`, `identifier`, `parentid` FROM `languages` WHERE `languageid` = ?";
    const SQL_LANG_BY_IDENTIFIER = "SELECT `languageid`, `identifier`, `parentid` FROM `languages` WHERE `identifier` = ?";
    const SQL_ALL_LANGS = "SELECT `languageid`, `identifier`, `parentid` FROM `languages`";

    protected $dbm;
    protected $cache;

    // Local caching so that we only have one instance of a language existing in memory at
    // any one time.
    protected $idMap;
    protected $identifierMap;

    public function __construct(db\DatabaseManager $dbm, Cache $cache = null) {
        $this->dbm = $dbm;
        $this->cache = $cache;
        $this->idMap = array();
        $this->identifierMap = array();
    }

    /**
     * Returns the language object of the language with the given id.
     */
    public function getLanguageById( $languageid ) {
        if( isset( $this->idMap[$languageid] ) )
            return $this->idMap[$languageid];

        if( $cache->isCached( 'lang_' . $languageid ) ) {
            $lang = $this->cache->get( 'lang_' . $languageid );
            $this->saveInLocalCache( $lang );
            return $lang;
        }

        $db = $this->dbm->getDb();

        $stmt = $db->prepare( self::SQL_LANG_BY_ID );
        $stmt->execute(array( $languageid ));
        $langData = $stmt->fetch(PDO::FETCH_ASSOC);
        $lang = Language::createFromArray( $langData );

        $this->cache( $lang );

        return $lang;

    }

    /**
     * Returns the language object of the language with the given string identifer
     */
    public function getLanguageByIdentifier( $identifier ) {
        if( isset( $this->identifierMap[$identifier] ) )
            return $this->identifierMap[$identifier];

        if( $this->cache->isCached( 'lang_ident_' . $identifier ) ) {
            $lang = $this->cache->get( 'lang_ident_' . $identifier );
            $this->saveInLocalCache( $lang );
            return $lang;
        }

        $db = $this->dbm->getDb();

        $stmt = $db->prepare( self::SQL_LANG_BY_IDENTIFIER );
        $stmt->execute(array( $identifier ));
        $langData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if( ! $langData['languageid'] )
            throw new exceptions\NonexistentLanguageException( $identifier );

        $lang = Language::createFromArray( $langData );

        $this->cache( $lang );

        return $lang;

    }

    /**
     * Returns all active languages in the language database.
     */
    public function getAllLanguages() {

        $db = $this->dbm->getDb();
    
        $stmt = $db->query( self::SQL_ALL_LANGS );
        $langArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $languages = array();

        foreach( $langArrays as $langData )
        {
            $langObject = Language::createFromArray( $langData );
            $this->cache( $langObject );
            array_push($languages, $langObject);
        }

        return $languages;
    }

    protected function cache( Language $lang ) {
        $this->cache->set('lang_' . $lang->getLanguageId(), $lang);
        $this->cache->set('lang_ident_' . $lang->getIdentifier(), $lang);
        $this->saveInLocalCache( $lang );
    }

    protected function saveInLocalCache( Language $lang ) {
        $this->idMap[$lang->getLanguageId()] = $lang;
        $this->identifierMap[$lang->getIdentifier()] = $lang;
    }

}
