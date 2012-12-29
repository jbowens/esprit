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
    use LogAware;

    /* The database manager this command should use for all connections to 
       the database systems */
    protected $databaseManager;

    /* The logger this command should use to log any events that occur during
       its execution. */
    protected $logger;

    /* The config options store to be used by the command */
    protected $config;

    /* The cache for the command to use */
    protected $cache;

    /* The view manager */
    protected $viewManager;

    /**
     * Gets the log source of this command. This is used by the
     * LogAware trait.
     */
    public function getLogSource()
    {
        $pieces = explode('\\', (string) get_class($this));
        if( count($pieces) == 0 )
            return null;
        else
            return $pieces[count($pieces)-1];
    }

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

    /**
     * Gets the datbase manager that gives the command access to the
     * databases.
     */
     protected function getDatabaseManager() {
         return $this->databaseManager;
     }

    /**
     * Gets the Cache object that should used by this command for
     * caching.
     */
    protected function getCache() {
        return $this->cache;
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

    /**
     * Gets the ViewManager that should be used for presentation of things that
     * are outside of the normal page display. Commands should not abuse this
     * access by displaying their templates through this pointer. This should only
     * be used for things like creating email templates, which require view decisions
     * but still belong in the model.
     */
    protected function getViewManager() {
        return $this->viewManager;
    }

    /**
     * Logs a message to this command's logger.
     *
     * @param $serverity  the severity of the log event (See constants defined in Logger)
     * @param $message  the message to log
     * @param $data  (optional) data related to the event
     */
    protected function log($severity, $message, $data = null) {
        $this->getLogger()->log( new LogEvent($severity, $this->getName(), $message, $data ) ); 
    }

    /**
     * Constructor for the base command. The default command resolvers instantiate
     * BaseCommands through this constructor.
     */
    public function __construct(Config $config, db\DatabaseManager $dbm, util\Logger $logger, Cache $cache, ViewManager $viewManager) {
        $this->config = $config;
        $this->databaseManager = $dbm;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->viewManager = $viewManager;
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
        return $this->run($request, $response);
    }

}
