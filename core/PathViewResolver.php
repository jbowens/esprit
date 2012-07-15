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

    const LOG_ORIGIN = "PATH_VIEW_RESOLVER";
    const DEFAULT_EXTENSION = 'php';

    /* The directories where the views are located */
    protected $viewDirectories;

    /* Config settings */
    protected $config;

    /* A logger to record errors to */
    protected $logger;

    /* The template parser to use */
    protected $templateParser;

    /* The extension used by the view files */
    protected $extension;

    public function __construct(array $dirs, Config $config, Logger $logger, TemplateParser $templateParser, $extension = self::DEFAULT_EXTENSION) {
        $this->viewDirectories = $dirs;
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
        $this->extension = $extension;

        // Validate the directories
        foreach( $this->viewDirectories as $key => $dir ) {
            if( ! is_dir( $dir ) ) {
                $this->logger->error($dir . " does not exist or is not a directory", self::LOG_ORIGIN);
                unset($this->viewDirectories[$key]);
            }
        }
    }

    /**
     * @see ViewResolver.resolve(Response $response)
     */
    public function resolve(Response $response) {
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

        $className = $this->getClassName( $viewStr );
        $filename = $className . '.' . $this->extension;

        foreach( $this->viewDirectories as $directory ) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

            if( file_exists( $filePath ) ) {

                require_once($filePath);

                $reflectionClass = new ReflectionClass($className);

                if( $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface('esprit\core\View') && 
                    $reflectionClass->isSubclassOf('esprit\core\AbstractView') ) {
                    return $reflectionClass->newInstance($this->config, $this->logger, $this->templateParser);
                }

             }

        }

        return null;
    }

} 
