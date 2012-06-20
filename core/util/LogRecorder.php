<?php

namespace esprit\core\util;

/**
 * An interface for a recorder that latches onto a Logger, recording
 * a subset of log events in some way.
 *
 * @author jbowens
 */
interface LogRecorder {

    /**
     * Determines whether this logger can accept the given event.
     *
     * @param LogEvent $event  a log event waiting to be recorded
     * @return true iff the LogRecorder can accept the event
     */
    public function canAccept(LogEvent $event);

    /**
     * Records the given LogEvent.
     *
     * @param LogEvent $event  the log event to record
     */
    public function record(LogEvent $event);

    /**
     * Closes the log recorder. This should be called as soon as the
     * recorder is no longer necessary.
     */
    public function close();

}
