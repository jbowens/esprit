<?php

namespace esprit\core\util;

/**
 * A factory for creating log events.
 *
 * @author jbowens
 */
class LogEventFactory {

    /**
     * Constructs a log event from an exception and a string representing
     * the origin of the event.
     *
     * @param Exception $e  the exception that occurred
     * @param $origin  a string representation of the log event source
     * @return a LogEvent for the exception
     */
    public static function createFromException(Exception $e, $origin) {
        return new LogEvent(Logger::ERROR, $origin, $e->getMessage(), $e);
    }

}
