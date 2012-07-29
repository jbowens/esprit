<?php

namespace esprit\core\debug;

use \esprit\core\LanguageSource as LanguageSource;
use \esprit\core\TranslationManager as TranslationManager;
use \esprit\core\db\DatabaseManager as DatabaseManager;

/**
 * A class that defines useful methods for updating translation strings on the site.
 *
 * @author jbowens
 */
class TranslationTool {

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

}
