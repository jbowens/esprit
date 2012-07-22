<?php

namespace esprit\core;

/**
 * A factory class for ViewResolvers.
 *
 * @author jbowens
 */
class ViewResolverFactory  {

    const DEFAULT_VIEW = '\esprit\core\views\DefaultView';
    
    protected $config;
    protected $logger;
    protected $templateParser;

    protected $espritSource;

    public function __construct(Config $config, util\Logger $logger, TemplateParser $templateParser) {
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
    }

    /**
     * Creates a new PathViewResolver.
     *
     * @param array $viewSources  (optional) an array of viewSources to include
     */
    public function createPathViewResolver(array $viewSources = array())
    {
        $resolver = new PathViewResolver($this->config, $this->logger, $this->templateParser);
        
        $resolver->registerSource( $this->getEspritSource() );

        foreach( $viewSources as $source ) {
            $resolver->registerSource( $source );
        }

        return $resolver;
    }

    /**
     * Creates a new CatchallViewResolver.
     *
     * @param View $view  the view to serve as the catchall.
     */
    public function createCatchallViewResolver( View $view = null ) {
        if( $view == null ) {
            $reflClass = new \ReflectionClass( self::DEFAULT_VIEW );
            $view = $reflClass->newInstance( $this->config, $this->logger, $this->templateParser );
        }
        return new CatchallViewResolver( $view );
    }

    /**
     * Gets a ViewSource for views that ship with Esprit and exist
     * within the \esprit\core\views namespace.
     */
    protected function getEspritSource() {

        if( $this->espritSource == null ) {
            $this->espritSource = new DefaultViewSource( $this->config,
                                                         $this->logger,
                                                         $this->templateParser,
                                                         '\esprit\core\views',
                                                         $this->config->get('esprit_views'));
        }

        return $this->espritSource;

    }

}
