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

    /* Cache of localized strings */
    protected $translationCache = array();

     /**
     * Retrieves the given text localized to the specified lanuage.
     *
     * @param string $translationIdentifier  the translation id of the text
     * @param string $language  the language identifier
     */
    public function getTranslation($translationIdentifier, $language) {


    }

}
