<?php

namespace esprit\core;

/**
 * This interface defines an interface for sources of localized strings.
 * Any sources of localized data should implement this interface.
 *
 * @author jbowens
 */
interface TranslationSource {

    /**
     * Retrieves the given text localized to the specified lanuage.
     *
     * @param string $translationIdentifier  the translation id of the text
     * @param string $language  the language identifier
     */
    public function getTranslation($translationIdentifier, $language);

}
