<?php

namespace esprit\core;

/**
 * A command resolver that uses the URL path of the request to search
 * for matching commands on the file system. For example, a request to
 * /about/team/bios would check for commands Command_About, Command_About_Team
 * and Command_About_Team_Bios. Dashes are converted into a following uppercase
 * letter like so: /about-us -> Command_AboutUs
 *
 * @author jbowens
 */
class PathCommandResolver implements CommandResolver {

    /* A list of directories to search */
    protected $commandDirectories = array();

    /* The extension used by commands */
    protected $extension;

    /**
     * Constructs a new path command resolver from an array of directories. The
     * resolver will search only the directories passed to its constructor when
     * resolving commands.
     *
     * @param array $directories  an array of strings (directories in the file system)
     * @param $extension  the extension of the php files in the directories
     */
    public function __construct($directories, $extension = 'php') {
        foreach( $directories as $dir )
            array_push($commandDirectories, $dir);
    }

    /**
     * Gets the full class name from the command string.
     */
    protected function getClassName( $commandStr ) {
        return 'Command_' . $commandStr;
    }

    /**
     * Searches the file system for a command that matches the path of
     * the requested url.
     *
     * @param Request $req  the request
     * @return Command  a matching command, or null if none was found
     */
    public function resolve(Request $req) {

        $url = $req->getUrl();
        $path = $url->getPath();

        // Treat the index as a special case
        if( $path == "/" || $path == "" || $url->getPathPiece(0) == "" ) {
            $command = $this->getCommand("Index");
            if( $command != null )
                return $command;
        }

        // Clean up the path pieces into class pieces
        $classPieces = array();
        for( $i = 0; $i < $url->getPathLength(); $i++ ) {
            $innerPieces = explode('-', $url->getPathPiece($i));
            $innerPieces = array_map('ucfirst', $innerPieces);
            array_push($classPieces, implode('', $innterPieces));
        }

        // Search for a matching command
        for( $i = count($classPieces)-1; $i >= 0; $i-- ) {
            
            $testPieces = array();
            for( $j = $i; $j >= 0; $j-- )
                array_push($testPieces, $classPieces[$j]);

            $potentialClass = implode('_', $testPieces);

            $command = $this->getCommand($potentialClass);
            
            if( $command != null )
                return $command;

        }

        return null;

    }

    /**
     * Retrieves a command from the command string.
     */
    protected function getCommand( $commandStr ) {
        $className = $this->getClassName();
        $filename = $className . '.' . $this->extension;

        foreach( $this->commandDirectories as $directory ) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

            if( file_exists( $filePath ) ) {

                require_once($filePath);

                $reflectionClass = new ReflectionClass($className);

                if( $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface("Command") )
                    return $reflectionClass->newInstance();

             }

        }

        return null;

    }

}
