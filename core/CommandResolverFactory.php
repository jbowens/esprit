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
    protected $dbm;

    public function __construct(Config $config, util\Logger $logger, db\DatabaseManager $dbm) {
        $this->config = $config;
        $this->logger = $logger;
        $this->dbm = $dbm;
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

        $espritCommandSource = new BaseCommandSource($this->config, $this->logger, $this->dbm, "\esprit\core\commands", $this->config->get('esprit_commands'));
        $resolver->registerSource( $espritCommandSource );

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
     * @param $classpath
     * @param $extension
     * @return a XmlCommandResolver  
     */
    public function createXmlCommandResolver($filepath, $classpath, $extension = null) {
        return new XmlCommandResolver($this->dbm, $this->config, $this->logger, $filepath, $classpath, $extension);
    }

}
