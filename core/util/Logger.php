<?php

namespace esprit\core\util;

/**
 * A logger utility class used throughout the framework to log events.
 *
 * @author jbowens
 */
class Logger {

    /* Log levels */
    const SEVERE = "SEVERE";
    const ERROR = "ERROR";
    const WARNING = "WARNING";
    const INFO = "INFO";
    const CONFIG = "CONFIG";
    const FINE = "FINE";
    const FINER = "FINER";
    const FINEST = "FINEST";

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
     * Logs a log event.
     *
     * @param $logEvent  the event to log
     */
    public function log(LogEvent $logEvent) {
        foreach( $this->logRecorders as $recorder ) {
            if( $recorder->canAccept( $logEvent ) )
                $recorder->record( $logEvent );
        }
    }

    /**
     * Log a warning message.
     */
    public function warning($message, $origin, $data = null) {
        $this->log( new LogEvent(self::WARNING,
                                 $origin,
                                 $message,
                                 $data) );
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

    /**
     * Returns all the log recorders attached to this Logger.
     */
    public function getRecorders() {
        return $this->logRecorders;
    }

    /**
     * Closes the logger, releasing any system resources held by the Logger,
     * BUT NOT ANY RESOURCES HELD BY RECORDERS. The recorders must be closed
     * separately as they may be attached to multiple Logger instances,
     */
    public void close() {
        // For the default logger implementation, nothing to do here.
    }

}


