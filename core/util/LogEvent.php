<?php

namespace esprit\core\util;

/**
 * This class describes some event that occured and should be logged.
 *
 * @author jbowens
 */
class LogEvent {

    /* The severity of the log event. See constants defined in Logger */
    protected $severity;

    /* A string representation of the origin of the log event */
    protected $origin;

    /* A string message describing the log event */
    protected $message;

    /* Data associated with the log event */
    protected $data;

    /* The timestamp of when the event was constructed */
    protected $timestamp;

    /**
     * Constructs a new LogEvent. You may want to use the LogEventFactory
     * class instead of instantiating your own LogEvents.
     *
     * @param $severity  one of the severity constants defined in Logger
     * @param $origin  a string representation of the origin of the log event
     * @param $message  the message outlining the event
     * @param $data  (optional) any relevant data associated with the log event.
     *                This should not include any unserializble objects (resources)
     */
    public function __construct($severity, $origin, $message, $data = null) {
        $this->severity = $severity;
        $this->origin = $origin;
        $this->message = $message;
        $this->data = $data;
        $this->timestamp = time();
    }

    /**
     * Returns the severity of this log event.
     *
     * @return the severity of the log event
     */
    public function getSeverity() {
        return $this->severity;
    }

    /**
     * Returns the origin of the log event.
     */
    public function getOrigin() {
        return $this->origin;
    }

    /**
     * Returns the message of the log event.
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Retrieves data associated with the log event.
     */
    public function getData() {
        return $data;
    }

    /**
     * Retrieves the timestamp associated with the log event.
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * Returns the string representation of this log event. LogRecorders
     * may use this when writing the log event to a file.
     *
     * @return  a human readable representation of the log event
     */
    public function toString() {
        return Logger::severityToString($this->getSeverity()) . " [".$this->getOrigin()."]: " . $this->getMessage();
    }

}
