<?php

namespace esprit\core;

use \Exception;
use \ReflectionClass;
use \SessionHandler;
use \SessionHandlerInterface;

use \esprit\core\exceptions\PageNotFoundException;
use \esprit\core\exceptions\UnserviceableRequestException;
use \esprit\core\utilFileLogRecorder;
use \esprit\core\util\Logger;
use \esprit\core\util\LogEventFactory;

/**
 * The primary controller for the framework. This controller creates the initial
 * request object and moves it along from model to view.
 * 
 * @author jbowens
 */
class Controller {

    const LOG_ORIGIN = "CONTROLLER";
    const DEFAULT_FALLBACK_COMMAND = '\esprit\core\commands\Command_DefaultFallback';
    const DEFAULT_TIMEZONE = 'America/New_York';

    /* The website the request came into */
    protected $site;

    /* The cache to use for storing data in memory between requests */ 
    protected $cache;

	/* The configuration object */
	protected $config;

    /* The logger used for logging major events */
    protected $logger;

    /* The datbase manager used by the controller */
    protected $dbm;

    /* The command resolvers to use */
    protected $commandResolvers;
	
    /* An optional custom session handler */
    protected $customSessionHandler;

    /* The view manager that handles presentation of responses */
    protected $viewManager;

    /* The language soruce to use throughout */
    protected $languageSource;

    /**
     * Creates a new controller from the given configuration object.
     *
     * @param Config $config  configuration settings
     */
    public static function createController(Config $config) {
        if( $config->get("debug") )
            return new debug\DebugController($config);
        else
            return new Controller($config);
    }

	/**
     * Creates a new controller from a configuration object. Do not use this directly.
     * Instead, use Controller::createController().
	 * 
	 * @param Config $config the config object to use
	 */
	public function __construct(Config $config) {
        try {
            $this->config = $config;

            // Set the timezone
            if( $this->config->settingExists('default_timezone') )
                date_default_timezone_set($this->config->get('default_timezone'));
            else
                date_default_timezone_set(self::DEFAULT_TIMEZONE);

            $this->logger = util\Logger::newInstance();
            if( $config->settingExists('default_error_logfile') )
               $this->logger->addLogRecorder(new util\FileLogRecorder($config->get('default_error_logfile'), Logger::WARNING));

            // Register a shutdown function so we can log any fatal errors
            register_shutdown_function(array($this, 'shutdown'));

            $this->cache = MemcachedCache::connectToMemcached($config->get('memcached_servers') ? $config->get('memcached_servers') : array(), $config, $this->logger);
            $this->commandResolvers = array();
            $this->customSessionHandler = null;

            $this->dbm = new db\DatabaseManager($config->get("db_default_dsn"),
                                                $config->get("db_default_user"),
                                                $config->get("db_default_pass"),
                                                $this->logger);

            $this->languageSource = new LanguageSource( $this->dbm, $this->logger, $this->cache ); 
            $this->site = $this->determineSite();

            $this->viewManager = new ViewManager($config, $this->logger, $this->createTranslationSource(), $this->site->getLanguage());
            $this->setupResolvers();
        } catch (Exception $ex)
        {
            // Log this
            if( $this->logger )
                $this->logger->log( LogEventFactory::createFromException( $ex, self::LOG_ORIGIN ) );
            $this->dieGracefully();
        }
    }

    /**
     * Setup resolvers from the configuration options.
     */
    protected function setupResolvers() {

        $default_resolvers = $this->config->get("default_resolvers");
        $cmdResolverFactory = $this->createCommandResolverFactory();
        $viewResolverFactory = $this->createViewResolverFactory();

        // Setup default command sources
        $commandSourceDefs = $this->config->settingExists("base_command_sources") ? $this->config->get("base_command_sources") : array();
        $commandSources = array( $this->createEspritCommandSource() );
        
        foreach( $commandSourceDefs as $def ) {
            array_push( $commandSources, $this->createBaseCommandSource( $def['namespace'], $def['directory'] ) );
        }
        
        // Setup default view sources
        $viewSourceDefs = $this->config->settingExists("default_view_sources") ? $this->config->get("default_view_sources") : array();
        $viewSources = array();
        foreach( $viewSourceDefs as $def ) {
            array_push( $viewSources, $this->createDefaultViewSource( $def['namespace'], $def['directory'] ) );
        }

        // Add the path command resolver
        if( isset($default_resolvers['use_path_command_resolver']) && $default_resolvers['use_path_command_resolver'] ) {
            $this->appendCommandResolver( $cmdResolverFactory->createPathCommandResolver( $commandSources ) ); 
        }

        // Add the path view resolver
        if( isset($default_resolvers['use_path_view_resolver']) && $default_resolvers['use_path_view_resolver'] ) {
            $this->appendViewResolver( $viewResolverFactory->createPathViewResolver( $viewSources ) );
        }

        // Add the xml view resolver
        if( isset($default_resolvers['xml_view_resolver_filepath']) ) {
            $this->appendViewResolver( $viewResolverFactory->createXmlViewResolver( $default_resolvers['xml_view_resolver_filepath'], 
                                                                                    $viewSources ) );
        }

        // Add the path catchall resolver
        if( isset($default_resolvers['use_catchall_view_resolver']) && $default_resolvers['use_catchall_view_resolver'] ) {
            $this->appendViewResolver( $viewResolverFactory->createCatchallViewResolver() );
        }

    }

    /**
     * Creates a new CommandResolverFactory.
     */
    public function createCommandResolverFactory() {
        return new CommandResolverFactory($this->config, $this->logger);
    }

    /**
     * Creats a new ViewResolverFactory.
     */
    public function createViewResolverFactory() {
        return new ViewResolverFactory($this->config, $this->logger, $this->viewManager->getTemplateParser());
    }

    /**
     * Creates a BaseCommandSource with the given data.
     */
    public function createBaseCommandSource($namespace, $directory) {
        return new BaseCommandSource($this->config, $this->logger, $this->dbm, $this->cache, $this->viewManager, $namespace, $directory);
    }

    /**
     * Creates a DefaultViewSource with the given data.
     */
    public function createDefaultViewSource($namespace, $directory) {
        return new DefaultViewSource($this->config,
                                     $this->logger,
                                     $this->viewManager->getTemplateParser(),
                                     $namespace,
                                     $directory);
    }

    /**
     * Appends a new command resolver onto the list of resolvers to use. Since resolvers
     * are used in the order that they're appended, you should make note of the order
     * you insert command resolvers.
     *
     * @param CommandResolver $resolver  a new resolver to use
     */
    public function appendCommandResolver(CommandResolver $resolver) {
        array_push($this->commandResolvers, $resolver);
    }

    /**
     * Appends a new view resolver onto the list of resolvers to use. Resolvers are used
     * in the order that they're appended.
     *
     * @param ViewResolver $resolver  a new resolver to use
     */
    public function appendViewResolver(ViewResolver $resolver) {
       $this->viewManager->addViewResolver( $resolver ); 
    }

    /**
     * Returns a command source that can instantiate all default Esprit commands.
     */
    public function createEspritCommandSource() {
        return new BaseCommandSource($this->config, 
                                     $this->logger, 
                                     $this->dbm, 
                                     $this->cache, 
                                     $this->viewManager, 
                                     '\esprit\core\commands', 
                                     $this->config->get('esprit_commands'));
    }

    /**
     * Frees up any system resources being held and prepares for exiting. You should always
     * call this when you're done with a Controller instance. After calling this method, you
     * cannot trust any Controller instantiated objects to still be in a usuable state as 
     * database connections, file handles, etc will all have been closed.
     */
    public function close() {

        $this->logger->fine("Closing controller", self::LOG_ORIGIN);

        try {
            $this->dbm->close();
        } catch( Exception $ex ) {
            $this->error($ex, 'closing DatabaseManager');
        }

        // Close any log recorders listening to the default logger
        try {
            foreach( $this->logger->getRecorders() as $recorder ) {
                $recorder->flushBuffer();
                $recorder->close();
            }
        } catch( Exception $ex ) {
            // Nothing we can do. The recorders are all gone.
        }

        try {
            $this->logger->close();
        } catch( Exception $ex ) {
            // Well shit. 
        }
    }

	/**
	 * Runs through the entire request to response cycle.
	 */
	public function run() {

        try {

            $this->initializeSessions();
            
            $request = $this->createRequestFromEnvironment();
            $response = new Response($request);

            $this->logger->finest("Request from " . $request->getIpAddress() . " to " . $request->getUrl()->getPath() . " " . date("r"), self::LOG_ORIGIN);

            // Identify the command that should be run
            $command = null;
            foreach( $this->commandResolvers as $resolver ) {
                $command = $resolver->resolve($request);
                if( $command != null )
                    break;
            }

            // If the request wasn't resolved to command, use the fallback.
            if( $command == null )
            {
                $command = $this->getFallbackCommand();

                $this->logger->info('Hit fallback command on request to ' . $request->getUrl()->getPath() , self::LOG_ORIGIN, $request); 
            }

            $this->logger->finest('Going to use command: ' . get_class($command), self::LOG_ORIGIN);

            try {
                $response = $this->executeCommand( $command, $request, $response );
            } catch( Exception $e ) {
                throw new UnserviceableRequestException( $request ); 
                $this->logger->log( LogEventFactory::createFromException( $e, 'Command\\'.$command->getName() ) );
            }

            $this->viewManager->display( $response );


        } catch( \esprit\core\exceptions\UnserviceableRequestException $exception ) {
            // Log this
            $this->logger->log( LogEventFactory::createFromException( $exception, self::LOG_ORIGIN ) );
            $this->dieGracefully();
        } catch( Exception $exception ) {
            // Don't expose internal details of the exception to the user. Just exit.
            $this->logger->log( LogEventFactory::createFromException( $exception, self::LOG_ORIGIN ) );
            $this->dieGracefully();
            return false;
        }

        return true;
            
    }

    /**
     * Executes the command, returning the new resulting response object.
     */
    protected function executeCommand(Command $command, Request $request, Response $response) {
        
        // Save the class name in case the view layer needs it
        $commandClassName = get_class( $command );
        $response->setCommandClass( $commandClassName );

        try
        {
            $response = $command->execute($request, $response);
        }
        catch( PageNotFoundException $e )
        {
            // Use the default fallback instead.
            $command = $this->getFallbackCommand();
            $response = $this->executeCommand( $command, $request, $response );
            $response->set404(true); 
        }

        return $response;
    }

    /**
     * Returns the Command that should be used as a fallback.
     */
    protected function getFallbackCommand() {
        if( $this->config->settingExists('FallbackCommand') ) {
            $fallbackCmdName = $this->config->get('FallbackCommand');
        } else {
            $fallbackCmdName = self::DEFAULT_FALLBACK_COMMAND;
        }

        $class = new ReflectionClass( $fallbackCmdName );
        if( ! $class->isSubclassOf('\esprit\core\BaseCommand') || ! $class->isInstantiable() )
            throw new \esprit\core\exceptions\UnserviceableRequestException( $request );
        
        $command = $class->newInstance($this->config, $this->dbm, $this->logger, $this->cache, $this->viewManager);

        return $command;
    }

	/**
	 * Processes PHP environment variables, instantiating and populating a
	 * Request object to represent the current HTTP request.
	 * 
	 * @return a Request object representing the received HTTP request
	 */
	public function createRequestFromEnvironment() {

        if( isset( $_SERVER['QUERY_STRING'][0] ) )
            $this->logger->finest("Received query string of " . $_SERVER['QUERY_STRING'], self::LOG_ORIGIN);

        //TODO: Update with support for actual site id
        if( strlen($_SERVER['QUERY_STRING']) )
            $qsLength = strlen($_SERVER['QUERY_STRING']) + 1;
        else
            $qsLength = 0;
        
        $url = new Url($_SERVER['SERVER_NAME'], substr($_SERVER['REQUEST_URI'], 0, strlen($_SERVER['REQUEST_URI']) - $qsLength), $_SERVER['QUERY_STRING']);
        $req = Request::createBuilder()->site($this->site)
                                       ->getData($_GET)
                                       ->postData($_POST)
                                       ->requestMethod($_SERVER['REQUEST_METHOD'])
                                       ->url( $url )
                                       ->headers(getallheaders())
                                       ->serverData($_SERVER)
                                       ->session( Session::createFromEnvironment() )
                                       ->build();	
		
		return $req;
		
	}

    /**
     * Returns the config used by the controller.
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Returns the logger used by the controller.
     *
     * @return util\Logger  the controller's logger
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Retrieves the session handler that should be used by the controller.
     *
     * @return a SessionHandlerInterface object
     */
    public function getSessionHandler() {
        if( $this->customSessionHandler != null)
            return $this->customSessionHandler;
        else
            return new SessionHandler();
    }

    /**
     * Sets a custom session handler to use. If this function is not called, the
     * default session handler defined in the php.ini will be used.
     *
     * @param SessionHandlerInterface $sessionHandler
     */
    public function setCustomSessionHandler(SessionHandlerInterface $sessionHandler) {
        $this->customSessionHandler = $sessionHandler;
    }

    /**
     * Sets session preferences and begins the session.
     */
    protected function initializeSessions() {
        $sessionHandler = $this->getSessionHandler();
        session_set_save_handler( $sessionHandler );
        session_start();
    }

    /**
     * Logs an error that occurred within the controller.
     *
     * @param Exception $ex  the exception of the error
     * @param $sourceDesc  a string describing the source of the error
     */
    protected function error(Exception $ex, $sourceDesc) {
        $this->logger->log( LogEventFactory::createFromException($ex, $sourceDesc) ); 
    }

    /**
     * This method may be called to abruptly end excecution but still print
     * an error message back to the user.
     */
    protected function dieGracefully( $message = null ) {

        if( ! $message )
            $message = 'The server was unable to service your request.';

        http_response_code(500);


        $html = <<<EOD
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - Esprit</title>
        <style type="text/css">
            body { background: #532424; color: #EEE; margin: 0; border-top: 20px #2e2e2e solid; }
            #errorPage {
                width: 800px;
                margin: 8em auto 0 auto;
                padding: 1em;
            }
            h1 { margin: .5em 0 1em 0; color: #FFF; font-family: Georgia, serif; text-shadow: 3px 1px 1px #111; }
            p { color: #EEE; line-height: 2; font-family: verdana, sans-serif; font-size: 1em; }
            p.errorMessage { color: #DDD; font-size: .9em; text-align: center; width: 75%; margin: 0 auto;}
            #notice { text-align: right; color: #784040; font-size: .7em; margin-top: 15em; font-family: verdana; }
            #notice a { color: #935757; text-decoration: none; }
            #notice a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div id="errorPage">
            <h1>Ruh-roh! Something went disastrously wrong!</h1>
            <p>$message In attempting to display this page, a fatal error occurred. The error has been logged and can be reviewed by the
               website administrator.</p>
            <div id="notice">
                This page was generated by <a href="https://github.com/jacksono/esprit">esprit</a>.
            </div>
        </div>
    </body>
    </html>
EOD;

        die( $html );
    }

    public function createTranslationSource() {
        return new TranslationManager($this->dbm, $this->cache, $this->languageSource); 
    }

    public function getTemplateParser() {
        return $this->viewManager->getTemplateParser();
    }

    /**
     * Determines which site the request came into.
     */
    public function determineSite() {
        // TODO: Actually implement
        return new Site(1, $_SERVER['HTTP_HOST'], $this->languageSource->getLanguageByIdentifier("en-US"));
    }

    /**
     * This method is called by PHP on shutdown. Do not call this yourself.
     */
    public function shutdown()
    {
        $error = error_get_last();

        if( $error != null )
        {
            $this->logger->severe("a fatal error occurred in " . $error['file'] . " on line " . $error['line'] . ": " . $error['message'], "SHUTDOWN");
        }

        // Ensure that the logs get flushed
        $this->logger->flushRecorders();
    }

}

