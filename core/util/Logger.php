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
    const FINE = 5;
    const FINER = 6;
    const FINEST = 7;

    /* Objects listening to the logs, and recording them */
    protected $logRecorders = array();

    /**
     *  A list of previously logged events. When a new log recorder is added, they'll
     *  receive all relevant previously logged log events.
     */
    protected $logEventBacklog = array();

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
        array_push( $this->logEventBacklog, $logEvent );
    }

    /**
     * Log a severe message.
     */
    public function severe($message, $origin, $data = null) {
        $this->log( new LogEvent(self::SEVERE,
                                 $origin,
                                 $message,
                                 $data) );
    }

    /**
     * Log an error message.
     */
    public function error($message, $origin, $data = null) {
        $this->log( new LogEvent(self::ERROR,
                                 $origin,
                                 $message,
                                 $data) );
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
     * Log an info message.
     */
    public function info($message, $origin, $data = null) {
        $this->log( new LogEvent(self::INFO,
                                 $origin,
                                 $message,
                                 $data) );
    }

    /**
     * Log a fine message.
     */
    public function fine($message, $origin, $data = null) {
        $this->log( new LogEvent( self::FINE,
                                  $origin,
                                  $message,
                                  $data) );
    }

    /**
     * Logs a finer message.
     */
    public function finer($message, $origin, $data = null) {
        $this->log( new LogEvent( self::FINER,
                                  $origin,
                                  $message,
                                  $data) );
    }

    /**
     * Logs a finest message.
     */
    public function finest($message, $origin, $data = null) {
        $this->log( new LogEvent( self::FINEST,
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

        // Inform the log about any previous log events
        foreach( $this->logEventBacklog as $previousEvent ) {
            if( $recorder->canAccept( $previousEvent ) )
                $recorder->record( $previousEvent );
        }
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
    public function close() {
        // Unset references
        $this->logEventBacklog = array();
        $this->logRecorders = array();
    }

    /**
     * If any log events recorded by the log event recorders have not yet
     * been flushed to their storage devices, this method will force them
     * to flush now.
     */
    public function flushRecorders() {
        // Flush the recorders
        foreach( $this->logRecorders as $recorder )
            $recorder->flushBuffer();
    }

    /**
     * Converts a severity constant into a string representing the severity.
     * This is messy as hell, but php doesn't support enums... I would like to
     * replace this at some point with something more elegant.
     */
    public static function severityToString( $severity ) {
        switch( $severity ) {
            case self::SEVERE:
                return "SEVERE";
            case self::ERROR:
                return "ERROR";
            case self::WARNING:
                return "WARNING";
            case self::INFO:
                return "INFO";
            case self::FINE:
                return "FINE";
            case self::FINER:
                return "FINER";
            case self::FINEST:
                return "FINEST";
            default:
                return "UNKNOWN";
        }
    }
}
