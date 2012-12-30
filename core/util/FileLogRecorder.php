<?php

namespace esprit\core\util;

/**
 * This class defines a simple LogRecorder that records all log events
 * of a specified severity and above to a file in their toString()
 * representations separated by newlines.
 */
class FileLogRecorder implements LogRecorder {
    use SeverityCutoff;    
   
    /* The file to record events in */
    protected $filename;

    /* The handle to opened log file */
    protected $fileHandle;

    /* A buffer for log events */
    protected $buffer = array();

    /**
     * Constructs a new file log recorder.
     *
     * @param $file  the filename of the file to write to
     * @param $severity  the required severity for an event to be recorded
     */
    public function __construct($file, $severity) {
        $this->setCutoff( $severity );
        $this->filename = $file;
        $this->fileHandle = fopen($file, "a");
        if( $this->fileHandle === false )
        {
            $this->close();
        }
    }

    /**
     * Writes all recorded log events to the file.
     */
    public function flushBuffer() {
        if( count($this->buffer) > 0 )
        {
            $bufferAsString = implode("\n", $this->buffer) . "\n";
            $this->buffer = array();
            if( $this->fileHandle )
                fwrite($this->fileHandle, $bufferAsString);
        }
    }

    /**
     * Records the LogEvent to the buffer
     */
    public function record(LogEvent $event) {
        if( $this->fileHandle )
            $this->buffer[] = $event->toString();
    }

    /**
     * Closes the FileLogRecorder and releases its system resources
     * like its open file handle.
     */
    public function close() {
        // Flush the buffer first
        $this->flushBuffer();

        // Close the file handle
        if( $this->fileHandle )
            fclose($this->fileHandle);
        $this->fileHandle = null;
    }

}
