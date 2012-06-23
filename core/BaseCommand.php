<?php

namespace esprit\core;

/**
 * An abstract base command that provides useful utilities. All commands
 * should most likely extend this class or one of its subclasses and not
 * implement Command directly.
 *
 * @author jbowens
 */
abstract class BaseCommand implements Command
{

    /* The database manager this command should use for all connections to 
       the database systems */
    protected $databaseManager;

    /* The logger this command should use to log any events that occur during
       its execution. */
    protected $logger;

    /* The config options store to be used by the command */
    protected $config;

    /**
     * Takes the incoming request data and produces the output data that
     * may be used by the view to produce the output. Any user-created
     * commands must override this abstract function. This is called by
     * execute(), which is called by the controller. All major business
     * logic for handling a request should be in this method.
     *
     * @param Request $request  the incoming request
     * @param Response $response  the data that should be used to form
     *                            the response
     * @return Response  the response object, modified
     */
    abstract function run(Request $request, Response $response);

    /*
     * Gets the datbase manager that gives the command access to the
     * databases.
     */
     protected function getDatabaseManager() {
         return $this->databaseManager;
     }

    /**
     * Gets this Config object that should be used by this command when
     * querying configuration settings.
     */
    protected function getConfig() {
        return $this->config;
    }

    /**
     * Gets the logger that this command should write its log events to
     */
     protected function getLogger() {
         return $this->logger;
     }

    public function __construct(Config $config, db\DatabaseManager $dbm, util\Logger $logger) {
        $this->config = $config;
        $this->databaseManager = $dbm;
        $this->logger = $logger;
    }

    /**
     * Runs the command. This function is called by the controller
     * directly and should probably not be overriden.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function execute(Request $request, Response $response) {
        return $this->run();
    }

}