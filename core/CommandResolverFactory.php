<?php

namespace esprit\core;

/**
 * A factory class for command resolvers.
 * 
 * @author jbowens
 */
class CommandResolverFactory {

    protected $config;
    protected $logger;

    public function __construct(Config $config, util\Logger $logger) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Creates a new PathCommandResolver.
     *
     * @see PathCommandResolver::__construct()
     *
     * @param array $commandSources  (optional) a list of command sources to automatically register
     * @return a PathCommandResolver
     */
    public function createPathCommandResolver(array $commandSources = array()) {
        $resolver = new PathCommandResolver($this->config, $this->logger); 

        foreach( $commandSources as $source )
            $resolver->registerSource( $source );

        return $resolver;
    }

    /**
     * Creates a new XmlCommandResolver for BaseCommands using this controller's
     * config, logger and db manager.
     *
     * @see XmlCommandResolver::__construct()
     *
     * @param $filepath
     * @param array $commandSources  (optional) an array of command sources to automatically register
     * @return a XmlCommandResolver  
     */
    public function createXmlCommandResolver($filepath, array $commandSources = array()) {
        
        $resolver = new XmlCommandResolver($this->config, $this->logger, $filepath);

        foreach( $commandSources as $source )
            $resolver->registerSource( $source );

        return $resolver;
    }

}
