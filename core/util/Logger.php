<?php

namespace esprit\core\util;

/**
 * A logger utility class used throughout the framework to log events.
 *
 * @author jbowens
 */
class Logger {

    /* Log levels */
    const SEVERE = 1;
    const ERROR = 2;
    const WARNING = 3;
    const INFO = 4;
    const CONFIG = 5;
    const FINE = 6;
    const FINER = 7;
    const FINEST = 8;

    /* Objects listening to the logs, and recording them */
    protected $logRecorders = array();

    /**
     * Create a new logger.
     */
    public static function newInstance() {
        return new Logger();
    }

    /**
     * Private constructor. Use Logger::newInstance() to get an instance of
     * the default logger.
     */
    protected function __construct() {

    }

    /**
     * Logs a message.
     *
     * @param $logLevel  the severity of the log event
     * @param $message  a message describing the event
     */
    public function log($logLevel, $message) {
    }

    /**
     * Add a log recorder to listen to this logger's log evnets.
     *
     * @param LogRecorder $recorder  the log recorder to add
     */
    public function addLogRecorder(LogRecorder $recorder) {
        if( ! in_array( $recorder, $this->logRecorders ) )
            array_push($this->logRecorders, $recorder);
    }

    /**
     * Removes a log recorder from the logger so that it no longer
     * receives this logger's log events.
     *
     * @param LogRecorder $recorder  the log recorder to remove
     */
     public function removeLogRecorder(LogRecorder $recorder) {
        $key = array_search($recorder, $this->logRecorders);
        if( $key !== false )
            unset( $this->logRecorders[$key] );
     }

}


