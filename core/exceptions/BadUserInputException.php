<?php

namespace esprit\core\exceptions;

/**
 * An exception thrown when bad input is received from the user.
 *
 * @author jbowens
 */
class BadUserInputException extends \InvalidArgumentException {

    protected $field;
    protected $message;

    public function __construct( $field, $message ) {
        $this->field = $field;
        $this->message = $message;

        parent::__construct( $message );
    }

    public function getField()
    {
        return $this->field;
    }

}
