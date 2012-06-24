<?php

namespace esprit\core;

/**
 * A command resolver that uses an xml file to map requests
 * to commands.
 *
 * NOTE: This command resolver only supports commands that extend BaseCommand.
 *
 * @jbowens
 */
class XmlCommandResolver {

    /* Filename to configure the resolver */
    protected $xmlFilename;

    /* The directories to search in for commands */
    protected $directories;

    /**
     * The mappings contained within the xml file. These
     * are lazy-loaded as needed.
     */
    protected $mappings = null;

    /* The php extension to use/expect */
    protected $extension;
 
    protected $dbm;
    protected $config;
    protected $logger;
    
    /**
     * Constructs a new XmlCommandResolver from an XML file outlining how
     * to resolve requests.
     *
     * @param $databaseManager  the database manager to construct BaseCommands with
     * @param $config  the config object to construct BaseCommands with
     * @param $logger  the logger to construct BaseCommands with
     * @param $filepath  the filepath to the file to use
     * @param $classpath  an array of the directories to search in for the
     *                      command files
     * @param $extension  the php filename extension to expect
     */
    public function __construct( db\DatabaseManager $databaseManager, Config $config, util\Logger $logger, $filepath, $classpath, $extension = 'php' ) {

        $this->dbm = $databaseManager;
        $this->config = $config;
        $this->logger = $logger;
        $this->xmlFilename = $filepath;
        $this->directories = $classpath;
		$this->extension = $extension;
		
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
  
        $filename = $command . '.' . $this->extension;

        foreach( $this->directories as $directory ) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

            if( file_exists( $filePath ) ) {

                require_once($filePath);

                $reflectionClass = new ReflectionClass($className);

                if( !$reflectionClass->isInstantiable() || !$reflectionClass->implementsInterface('esprit\core\Command') 
                    || !$reflectionClass->isSubclassOf('esprit\core\BaseCommand') ) {
                	throw new Exception($command . " is not an instantiable BaseCommand.");
                }
                
                return $reflectionClass->newInstance($this->config, $this->dbm, $this->logger);

             }

        }

        return null;

    }

}
