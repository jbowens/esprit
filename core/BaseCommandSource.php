<?php

namespace esprit\core;

/**
 * A default command source for BaseCommands.
 *
 * @author jbowens
 */
class BaseCommandSource implements CommandSource {

    const FILE_EXTENSION = 'php';

    protected $config;
    protected $logger;
    protected $dbm;
    protected $cache;
    protected $namespace;
    protected $directory;

    /**
     * Creates a new BaseCommandSource. This source will look in the given directory for commands. The provided
     * namespace should be the namespace of all commands within the directory.
     */
    public function __construct(Config $config, util\Logger $logger, db\DatabaseManager $dbm, Cache $cache, $namespace, $directory) {
        $this->config = $config;
        $this->logger = $logger;
        $this->dbm = $dbm;
        $this->cache = $cache;
        $this->namespace = $namespace;
        $this->directory = $directory;
    }

    /**
     * See CommandSource.isCommandDefined()
     */
    public function isCommandDefined( $commandName ) {
        $className = $this->getClassName( $commandName );
        $filename = $className . '.' . self::FILE_EXTENSION;
        $absolutePath = $this->directory . DIRECTORY_SEPARATOR . $filename;

        // Make sure the file actually exists
        if( ! file_exists($absolutePath) )
            return false;

        require_once($absolutePath);

        $reflectionClass = new \ReflectionClass($this->namespace . "\\" . $className);

        if( $reflectionClass->isInstantiable() && $reflectionClass->implementsInterface('esprit\core\Command') && 
            $reflectionClass->isSubclassOf('esprit\core\BaseCommand') )
            return true;
        else
            return false;

    }

    /**
     * See CommandSource.instantiateCommand()
     */
    public function instantiateCommand( $commandName ) {
        $className = $this->getClassName( $commandName );

        $reflectionClass = new \ReflectionClass( $this->namespace . "\\" . $className);
        return $reflectionClass->newInstance($this->config, $this->dbm, $this->logger, $this->cache);

    }

    /**
     * Gets the class name of a command from its command name
     */
    protected function getClassName( $commandName ) {
        return 'Command_'.$commandName;
    }

}
