<?php

namespace esprit\core;

use \ReflectionClass as ReflectionClass;

/**
 * A command resolver that uses the URL path of the request to search
 * for matching commands on the file system. For example, a request to
 * /about/team/bios would check for commands Command_About, Command_About_Team
 * and Command_About_Team_Bios. Dashes are converted into a following uppercase
 * letter like so: /about-us -> Command_AboutUs.
 *
 * NOTE: This command resolver only supports commands that extend BaseCommand.
 *
 * @author jbowens
 */
class PathCommandResolver implements CommandResolver {

    const LOG_SOURCE = "PathCommandResolver";

    /* A list of sources to use */
    protected $commandSources;

    protected $config;
    protected $logger;

    /**
     * Constructs a new PathCommandResolver. Command sources need to be registered with the resolver
     * for the resolver to actually resolve to any commands.
     */
    public function __construct(Config $config, util\Logger $logger) {
        $this->config = $config;
        $this->logger = $logger;

        $this->commandSources = array();
    }

    /**
     * Adds a command source to the list of sources used by the resolver.
     *
     * @param CommandSource $commandSource  another command source to use
     */
    public function registerSource(CommandSource $commandSource) {
        array_unshift($this->commandSources, $commandSource);
    }

    /**
     * Searches the command sources for a command that matches the path of
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

            $command = $this->getCommand( 'Index' ); 

            if( $command == null ) {
                // No command sources had an index command
                $this->logger->error("No Index command found in the given command sources", self::LOG_SOURCE);
            }

            return $command;
        }

        // Clean up the path pieces into class pieces
        $classPieces = array();
        for( $i = 0; $i < $url->getPathLength(); $i++ ) {
            $innerPieces = explode('-', $url->getPathPiece($i));
            $innerPieces = array_map('ucfirst', $innerPieces);
            array_push($classPieces, implode('', $innerPieces));
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

        foreach( $this->commandSources as $source )
        {
            if( $source->isCommandDefined( $commandStr ) )
            {
                return $source->instantiateCommand( $commandStr );
            }
        }

        // The command is not defined in any of the provided command sources
        return null;
    }

}
