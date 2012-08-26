<?php

namespace esprit\core;

/**
 * A default ViewSource that can search a directory for views that
 * extend the AbstactView class and instantiate them on the fly.
 *
 * @author jbowens
 */
class DefaultViewSource implements ViewSource {

    const LOG_SOURCE = "DEFAULT_VIEW_SOURCE";
    const FILE_EXTENSION = 'php';

    protected $config;
    protected $logger;
    protected $templateParser;
    protected $namespace;
    protected $directory;

    /**
     * Constructs a DefaultViewSource
     *
     * @param $namespace  the namespace of views this source provides
     * @param $directory  the directory this source should look in for its views
     */
    public function __construct(Config $config, util\Logger $logger, TemplateParser $templateParser, $namespace, $directory) {
        $logger->fine("Creating DefaultViewSource for ".$namespace." namespace and the directory " . $directory, self::LOG_SOURCE);
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
        $this->namespace = $namespace;
        $this->directory = $directory;
    }

    // @See ViewSource.isViewDefined()
    public function isViewDefined( $viewName ) {

        $this->logger->finest('Checking for existence of view ' . $viewName, self::LOG_SOURCE);

        // If there's a namespace extract the class name and verify that the namespaces
        // match 
        if( stripos( $viewName, "\\" ) !== false )
        {
            // There's a namespace included in the view name, so this namespace must match exactly
            $clippedSourceNamespace = ($this->namespace[0] == "\\") ? substr($this->namespace, 1) : $this->namespace;
            $clippedView = ($viewName[0] == "\\") ? substr($viewName, 1) : $viewName;

            $namePieces = explode("\\", $viewName);
            $className = $namePieces[count($namePieces)-1];
            unset($namePieces[count($namePieces)-1]);
            $namespace = implode("\\", $namePieces);

            // Check the namespaces
            if( $namespace != $clippedSourceNamespace )
            {
                // The namespaces did not match
                $this->info( "The namespace for " . $viewName . " did not match " . $this->namespace, self::LOG_SOURCE );
                return false;
            }

            $viewName = $className;
        }

        $className = $this->getClassName( $viewName );
        $filename = $className . '.' . self::FILE_EXTENSION;
        $absolutePath = $this->directory . DIRECTORY_SEPARATOR . $filename;

        $this->logger->finest("Looking for file " . $absolutePath, self::LOG_SOURCE);

        // Make sure the file actually exists
        if( ! file_exists($absolutePath) )
            return false;

        require_once($absolutePath);

        $reflectionClass = new \ReflectionClass($this->namespace . "\\" . $className);

        if( $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface('esprit\core\View') && 
            $reflectionClass->isSubclassOf('esprit\core\AbstractView') )
            return true;
        else
            return false;
    }

    // @See ViewSource.instantiateView()
    public function instantiateView( $viewName ) {

        // Extract the class name if there's a namespace in here
        if( stripos( $viewName, "\\" ) !== false )
        {
            // There's a namespace included in the view name, so this namespace must match exactly
            $clippedSourceNamespace = ($this->namespace[0] == "\\") ? substr($this->namespace, 1) : $this->namespace;
            $clippedView = ($viewName[0] == "\\") ? substr($viewName, 1) : $viewName;

            $namePieces = explode("\\", $viewName);
            $viewName = $namePieces[count($namePieces)-1];
        }

        $className = $this->getClassName( $viewName );

        $reflectionClass = new \ReflectionClass( $this->namespace . "\\" . $className);
        return $reflectionClass->newInstance($this->config, $this->logger, $this->templateParser);
    }

    protected function getClassName( $viewName ) {
        return $viewName;
    }
}
