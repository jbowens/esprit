<?php

namespace esprit\core;

use \esprit\core\util\Logger as Logger;

/**
 * A view resolver that determines which view to use based on the requested
 * url. This class parallels PathCommandResolver. This resolver can only create
 * views that extend AbstractView.
 *
 * @author jbowens
 */
class PathViewResolver implements ViewResolver {
    use LogAware;

    const LOG_ORIGIN = "PATH_VIEW_RESOLVER";
    const VIEW_NAME_404 = "FourOhFour";

    /* The sources for views */
    protected $viewSources;

    /* Config settings */
    protected $config;

    /* A logger to record errors to */
    protected $logger;

    /* The template parser to use */
    protected $templateParser;

    public function __construct(Config $config, Logger $logger, TemplateParser $templateParser) {
        $this->viewSources = array();
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
    }

    /**
     * Register a source for views.
     *
     * @param ViewSource $source  the source to register
     */
    public function registerSource(ViewSource $source) {
        array_unshift($this->viewSources, $source);
    }

    /**
     * @see ViewResolver.resolve(Response $response)
     */
    public function resolve(Response $response) {
        
        // Handle 404 case
        if( $response->get404() )
        {
            $view = $this->getView('FourOhFour');
            if( $view != null )
                return $view;
        }

        $url = $response->getRequest()->getUrl();
        $path = $url->getPath();

        // Treat the index as a special case
        if( $path == "/" || $path == "" || $url->getPathPiece(0) == "" ) {
            $view = $this->getView("Index");
            if( $view != null )
                return $view;
        }

        // Clean up the path pieces into class pieces
        $classPieces = array();
        for( $i = 0; $i < $url->getPathLength(); $i++ ) {
            $innerPieces = explode('-', $url->getPathPiece($i));
            $innerPieces = array_map('ucfirst', $innerPieces);
            array_push($classPieces, implode('', $innerPieces));
        }

        // Search for a matching view
        for( $i = count($classPieces)-1; $i >= 0; $i-- ) {
            
            $testPieces = array();
            for( $j = $i; $j >= 0; $j-- )
                array_push($testPieces, $classPieces[$j]);

            $potentialClass = implode('_', $testPieces);

            $view = $this->getView($potentialClass);
            
            if( $view != null )
                return $view;

        }

        return null;
    }

    /**
     * Retrieves a view from a string of its name.
     *
     * @param $viewStr  the view to instantiate
     */
    protected function getView( $viewStr ) {
        
        foreach( $this->viewSources as $source )
        {
            if( $source->isViewDefined( $viewStr ) )
            {
                return $source->instantiateView( $viewStr );
            }
        }

        return null;
    }

} 
