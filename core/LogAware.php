<?php

namespace esprit\core;

use util\Logger;

/**
 * This trait exists for anything that has a util\Logger instance. It provides
 * convenience methods to access the logger through $this, abstracting out the
 * log source.
 *
 * @author jbowens
 * @since 2012-08-26
 */
trait LogAware {

    /**
     * Returns the string to be used as the log source.
     */
    protected function getLogSource()
    {
        return get_class($this);    
    }

    /**
     * Logging forwarders
     */

    protected function severe($message, $data = null)
    {
        $this->logger->severe($message, $this->getLogSource(), $data);
    }

    protected function error($message, $data = null)
    {
        $this->logger->error($message, $this->getLogSource(), $data);
    }

    protected function warning($message, $data = null)
    {
        $this->logger->warning($message, $this->getLogSource(), $data);
    }

    protected function info($message, $data = null)
    {
        $this->logger->info($message, $this->getLogSource(), $data);
    }

    protected function fine($message, $data = null)
    {
        $this->logger->fine($message, $this->getLogSource(), $data);
    }

    protected function finer($message, $data = null)
    {
        $this->logger->finer($message, $this->getLogSource(), $data);
    }

    protected function finest($message, $data = null)
    {
        $this->logger->finest($message, $this->getLogSource(), $data);
    }

}
