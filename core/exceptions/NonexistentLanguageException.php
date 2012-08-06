<?php

namespace esprit\core\exceptions;

class NonexistentLanguageException extends \InvalidArgumentException {

    protected $languageIdentifier;

    public function __construct($id) {
        $this->languageIdentifier = $id;
        parent::__construct("[LANG] The language " + $id + " does not exist.", 0);
    }

}
