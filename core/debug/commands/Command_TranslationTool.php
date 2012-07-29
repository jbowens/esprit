<?php

namespace esprit\core\debug\commands;

use \esprit\core\Config;
use \esprit\core\db\DatabaseManager;
use \esprit\core\util\Logger;
use \esprit\core\Cache;
use \esprit\core\debug\TranslationTool;
use \esprit\core\BaseCommand as BaseCommand;
use \esprit\core\Request as Request;
use \esprit\core\Response as Response;

/**
 * A command that handles the translation tool.
 *
 * @author jbowens 
 */
class Command_TranslationTool extends BaseCommand {

    const COMMAND_NAME = "Cmd_TranslationTool";

    protected $translationTool;

    public function __construct(Config $config, DatabaseManager $dbm, Logger $logger, Cache $cache, TranslationTool $translationTool) {
        parent::__construct($config, $dbm, $logger, $cache);
        $this->translationTool = $translationTool;
    }  

    /**
     * See BaseCommand.run(Request $request, Response $response) 
     */
    public function run(Request $request, Response $response) {

        $languages = $this->translationTool->getLanguages();
        $response->set('languages', $languages);         

        return $response;

    }

    public function getName() {
        return self::COMMAND_NAME;
    }

} 
