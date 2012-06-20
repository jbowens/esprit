<?php

namespace esprit\core\util;

/**
 * Defines a trait for LogRecorders that when mixed in makes the LogRecorder
 * accept only events of the severity specified on construction or higher.
 *
 * @author jbowens
 */
trait SeverityCutoff {

    /* The minimum severity necessary to be recorded */
    protected $severityCutoff;

    /**
     * Sets the severity cut off the class should accept at.
     *
     * @param $severity  one of the severity constants defined in Logger
     */
    public function setCutoff( $severity ) {
        $this->severityCutoff = $severity;
    }

    /**
     * Determines whether this severity cutoff accepts an event by
     * checking if its severity is high enough. 
     */
    public function canAccept( LogEvent $e ) {
        return $e->getSeverity() <= $this->severityCutoff;
    }

}
