<?php

namespace esprit\core\debug;

use \esprit\core\Language as Language;
use \esprit\core\LanguageSource as LanguageSource;
use \esprit\core\TranslationManager as TranslationManager;
use \esprit\core\db\DatabaseManager as DatabaseManager;

/**
 * A class that defines useful methods for updating translation strings on the site.
 *
 * @author jbowens
 */
class TranslationTool {

    const SQL_CHECK_TRANSLATION_EXISTENCE = "SELECT `translationid` FROM `translations` WHERE `languageid` = ? AND `translationIdentifier` = ?";
    const SQL_UPDATE_TRANSLATION = "UPDATE `translations` SET `translation` = ? WHERE `languageid` = ? AND `translationIdentifier` = ?";
    const SQL_INSERT_TRANSLATION = "INSERT INTO `translations` (`translationIdentifier`,`languageid`,`translation`) VALUES(?, ?, ?)";
    const SQL_CHECK_TRANSLATION_IDENTIFIER = "SELECT COUNT(translationid) FROM `translations` WHERE `translationIdentifier` = ?";

    protected $dbm;
    protected $langSource;
    protected $translationManager;

    public function __construct(DatabaseManager $dbm, LanguageSource $langSource, TranslationManager $translationManager) {
        $this->dbm = $dbm;
        $this->langSource = $langSource;
        $this->translationManager = $translationManager;
    }

    /**
     * Gets languages relevant for translation.
     *
     * @return array of Language objects
     */
    public function getLanguages() {
       return $this->langSource->getAllLanguages(); 
    }

    /**
     * Given a suggested or desired translation identifier, this method will return a unique translation
     * identifier that does not yet exist.
     *
     * @param $suggestedIdentifier
     * @return a unique translation identifier 
     */
    public function getNewIdentifier( $suggestedIdentifier ) {

        if( ! $this->identifierExists( $suggestedIdentifier ) )
            return $suggestedIdentifier;

        $pieces = explode( '_', $suggestedIdentifier );
        $lastPiece = $pieces[count($pieces)-1];

        $iteration = 0;
        if( strlen($lastPiece) && $lastPiece[0] == '*' ) {
            $iteration = intval(substr($lastPiece, 1), 16);
            unset( $pieces[count($pieces)-1] );
        }

        $base = implode( '_', $pieces );
        $guess = $base . '_*' . dechex( ++$iteration );

        while( $this->identifierExists($guess) ) {
            $iteration++; 
            $guess = $base . '_*' . dechex( $iteration );
        }

        return $guess;
    }

    /**
     * Checks to see if a given translation identifier already exists.
     *
     * @param $translationIdentifier  a potential translation identifier
     */
    public function identifierExists( $translationIdentifier ) {
        $db = $this->dbm->getDb();
        $checkStmt = $db->prepare( self::SQL_CHECK_TRANSLATION_IDENTIFIER );
        $checkStmt->execute( array( $translationIdentifier ) );
        return $checkStmt->fetchColumn() > 0;
    }

    /**
     * Saves a new translation in the database.
     *
     * @param Language $language  the language to add the translation for
     * @param $tIdentifier  the translation identifier
     * @poram $translation  the translation
     */
    public function setTranslation(Language $language, $tIdentifier, $translation) {
        $db = $this->dbm->getDb();
        $rowExistStmt = $db->prepare( self::SQL_CHECK_TRANSLATION_EXISTENCE );
        $rowExistStmt->execute(array( $language->getLanguageId(), $tIdentifier ));
        if( $rowExistStmt->fetchColumn() ) {
            $updateStmt = $db->prepare( self::SQL_UPDATE_TRANSLATION );
            $updateStmt->execute(array( $translation, $language->getLanguageId(), $tIdentifier ));
        } else {
            $insertStmt = $db->prepare( self::SQL_INSERT_TRANSLATION );
            $insertStmt->execute(array( $tIdentifier, $language->getLanguageId(), $translation ));
        }
    }

}
