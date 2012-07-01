<?php

namespace esprit\core;

/**
 * Represents a language in the translation system.
 *
 * @author jbowens
 */
class Language {

    protected $languageid;          // Numerical unique identifer
    protected $identifier;          // String unique identifer
    protected $parentid;            // Numerical id of the ancestor, or null if a root

    /**
     * If possible, use one of the static factory methods instead of using this
     * constructor explicitly.
     */
    public function __construct($id, $strIdentifer, $parentid) {
        $this->locationid = $id;
        $this->identifier = $strIdentifer;
        $this->parentid = $parentid;
    }

    public function getLanguageId() {
        return $this->languageid;
    }

    public function getIdentifer() {
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
        return new Language($arr['languageid'], $arr['identifer'], $arr['parentid']);
    }

}
