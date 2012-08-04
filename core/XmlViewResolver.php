<?php

namespace esprit\core;

/**
 * A view resolver that uses an xml file to map commands
 * to matching views.
 *
 * @jbowens
 */
class XmlViewResolver implements ViewResolver {

    const LOG_SOURCE = "XML_VIEW_RESOLVER";

    /* Filename to configure the resolver */
    protected $xmlFilename;

    /* The view sources to search in for views */
    protected $viewSources;

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
        $this->viewSources = array();

    }

    /**
     * Registers a view source with the resolver so that views from the source
     * may be used when resolving views.
     *
     * @param ViewSource $source  the view source to register
     */
    public function registerSource( ViewSource $source ) {
        array_unshift( $this->viewSources, $source );
    }

    /**
     * See ViewResolver::resolve(Request $req)
     */
    public function resolve(Response $resp) {

        if( ! $this->areMappingsLoaded() )
            $this->loadMappings();

        foreach( $this->mappings as $mapping )
        {

            // Check if the mapping is applicable
            if( $mapping['command'] == $resp->getCommandClass() )
            {
                // This mapping applies
                return $this->getView( $mapping['view'] );
            }

        }

        return null;

    }

    /**
     * Determines if the mappings have been loaded from the xml file.
     */
    protected function areMappingsLoaded() {
        return ($this->mappings != null);
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

            foreach( $xmlMappings as $mapping )
            {
                $mapArr = array();
                $mapArr['command'] = (string) $mapping->command[0];
                $mapArr['view'] = (string) $mapping->view[0];
                array_push($this->mappings, $mapArr);
            }

        } catch(Exception $e) {
            throw new exceptions\ResourceLoadingException("Unable to parse " . $this->xmlFilename . ": " . $e->getMessage());
        }

    }

    /**
     * Creates a view given the class name of a view.
     * 
     * @param $view  the class name of the view
     * @return an instance of the view
     */
    protected function getView( $view ) {

        foreach( $this->viewSources as $source )
        {
            if( $source->isViewDefined( $view ) )
            {
                return $source->instantiateView( $view );
            }
        }

        $this->logger->error("Could not find view " . $view, self::LOG_SOURCE);

        return null;

    }

}
