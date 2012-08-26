<?php

namespace esprit\core;

/**
 * A command resolver that uses an xml file to map requests
 * to commands.
 *
 * @jbowens
 */
class XmlCommandResolver implements CommandResolver {
    use LogAware;

    /* Filename to configure the resolver */
    protected $xmlFilename;

    /* The command sources to search in for commands */
    protected $commandSources;

    /**
     * The mappings contained within the xml file. These
     * are lazy-loaded as needed.
     */
    protected $mappings = null;

    protected $config;
    protected $logger;
    
    /**
     * Constructs a new XmlCommandResolver from an XML file outlining how
     * to resolve requests.
     *
     * @param $config  an esprit config object
     * @param $logger  an esprit logger
     * @param $filepath  the filepath to the file to use
     */
    public function __construct( Config $config, util\Logger $logger, $filepath ) {

        $this->config = $config;
        $this->logger = $logger;
        $this->xmlFilename = $filepath;
        $this->commandSources = array();

    }

    /**
     * Registers a command source with the resolver so that commands from the source
     * may be used when resolving commands.
     *
     * @param CommandSource $source  the command source to register
     */
    public function registerSource( CommandSource $source ) {
        array_unshift( $this->commandSources, $source );
    }

    /**
     * See CommandResolver::resolve(Request $req)
     */
    public function resolve(Request $req) {

        if( ! $this->areMappingsLoaded() )
            $this->loadMappings();

        $url = $req->$getUrl();

        foreach( $this->mappings as $mapping ) {

            // Check if the mapping is applicable
            if( $mapping['url'] == $url->getPath() || 
                ($this->urlFuzzyMatches($mapping['url'], $url) && !$mapping['requireExactMatch']) ) {
                // This mapping applies
                return $this->getCommand( $mapping['command'] );
            }

        }

        return null;

    }

    /**
     * Determines if the mappings have been loaded from the xml file.
     */
    protected function areMappingsLoaded() {
        return ($mappings != null);
    }

    /**
     * Loads the mappings from the xml file.
     *
     * @throws ResourceLoadingException  on failure
     */
    protected function loadMappings() {
        
        try {

            $doc = simplexml_load_file($this->xmlFilename);

            $xmlMappings = $doc->xpath('/app/mapping');

            $this->mappings = array();

            foreach( $xmlMappings as $mapping ) {
                $mapArr = array();
                $mapArr['url'] = (string) $mapping->url[0];
                $mapArr['command'] = (string) $mapping->command[0];
                $mapArr['requireExactMatch'] = empty($mapping->requireExactMatch) ? false : true;
                array_push($this->mappings, $mapArr);
            }

        } catch(Exception $e) {
            
            throw new exceptions\ResourceLoadingException("Unable to parse " . $this->xmlFilename . ": " . $e->getMessage());

        }

    }

    /**
     * Determines if the mapping matches any subdirectory of the 
     * requested url.
     */
    protected function urlFuzzyMatches($mapping, $url) {

        $mapPieces = explode('/', strtolower($mapping['url']));

        if( count($mapPieces) > $url->getPathLength() )
            return false;

        foreach( $mapPieces as $key => $piece ) {
            if( $piece != strtolower($url->getPathPiece($key)) )
                return false;
        }

        return true;

    }

    /**
     * Creates a command given the class name of a command.
     * 
     * @param $command  the class name of the command
     * @return an instance of the command
     */
    protected function getCommand( $command ) {

        foreach( $this->commandSources as $source )
        {
            if( $source->isCommandDefined( $command ) )
            {
                return $source->instantiateCommand( $command );
            }
        }

        return null;

    }

}
