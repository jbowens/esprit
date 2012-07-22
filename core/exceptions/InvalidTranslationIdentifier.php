<?php

namespace esprit\core\exceptions;

/**
 * An exception thrown when a queried translation identifier doesn't actually exist.
 *
 * @author jbowens
 */
class InvalidTranslationIdentifier extends \InvalidArgumentException {

    protected $translationIdentifier;

    public function __construct( $identifier ) {
        $this->translationIdentifier = $identifier;

        parent::__construct("[TRANS] Queried non-existent translation identifier: " . $this->translationIdentifier);
    }

}
