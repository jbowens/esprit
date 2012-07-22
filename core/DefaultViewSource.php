<?php

namespace esprit\core;

/**
 * A default ViewSource that can search a directory for views that
 * extend the AbstactView class and instantiate them on the fly.
 *
 * @author jbowens
 */
class DefaultViewSource implements ViewSource {

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
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
        $this->namespace = $namespace;
        $this->directory = $directory;
    }

    // @See ViewSource.isViewDefined()
    public function isViewDefined( $viewName ) {
        $className = $this->getClassName( $viewName );
        $filename = $className . '.' . self::FILE_EXTENSION;
        $absolutePath = $this->directory . DIRECTORY_SEPARATOR . $filename;

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
        $className = $this->getClassName( $viewName );

        $reflectionClass = new \ReflectionClass( $this->namespace . "\\" . $className);
        return $reflectionClass->newInstance($this->config, $this->logger, $this->templateParser);
    }

    protected function getClassName( $viewName ) {
        return "View_" . $viewName;
    }
}
