<?php

namespace esprit\core;

/**
 * Represents a language in the translation system. This class is immutable.
 *
 * @author jbowens
 */
class Language {

    private $languageid;          // Numerical unique identifer
    private $identifier;          // String unique identifer
    private $parentid;            // Numerical id of the ancestor, or null if a root

    /**
     * If possible, use one of the static factory methods instead of using this
     * constructor explicitly.
     */
    public function __construct($id, $strIdentifer, $parentid) {
        $this->languageid = $id;
        $this->identifier = $strIdentifer;
        $this->parentid = $parentid;
    }

    public function getLanguageId() {
        return $this->languageid;
    }

    public function getIdentifier() {
        return $this->identifier;
    }

    public function getParentId() {
        return $this->parentid;
    }

    /**
     * Creates a language object from an associative array of the
     * language's attributes. This function expects the array to
     * come straight from the database.
     */
    public static function createFromArray( $arr ) {
        return new Language($arr['languageid'], $arr['identifier'], $arr['parentid']);
    }

}
