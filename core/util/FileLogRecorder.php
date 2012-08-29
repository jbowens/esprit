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
     * @Override
     *
     * Records the LogEvent to a file.
     */
    public function record(LogEvent $event) {
        if( $this->fileHandle )
            fwrite($this->fileHandle, $event->toString() . "\n");
    }

    /**
     * Closes the FileLogRecorder and releases its system resources
     * like its open file handle.
     */
    public function close() {
        if( $this->fileHandle )
            fclose($this->fileHandle);
        $this->fileHandle = null;
    }

}
