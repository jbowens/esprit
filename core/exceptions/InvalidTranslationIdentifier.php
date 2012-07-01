<?php

namespace esprit\core\exceptions;

/**
 * An exception thrown when a queried translation identifier doesn't actually exist.
 *
 * @author jbowens
 */
class InvalidTranslationIdentifier extends InvalidArgumentException {

    protected $translationIdentifier;

    public function __cosntruct( $identifier ) {
        $this->translationIdentifier = $identifier;
    }

    public function getMessage() {
        return "[TRANS] Queried non-existent translation identifier: " . $this->translationIdentifier;
    }

}
