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
        
        if( $request->getGet('do') == "create-string" )
        {
            $identifier = $this->translationTool->getNewIdentifier( $request->getPost('suggested_identifier') );
            $languagesTranslated = 0;
            foreach( $languages as $l ) {
                if( $request->getPost( 'use_t_' . $l->getLanguageId() ) && $request->postParamExists('t_'. $l->getLanguageId()) ) {
                    $this->translationTool->setTranslation( $l, $identifier, $request->getPost('t_' . $l->getLanguageId()) );
                    $languagesTranslated++;
                }
            }
            $response->set('newTranslationIdentifier', $identifier);
            $response->set('languagesTranslated', $languagesTranslated);
        }
        else
        {
            $response->set('languages', $languages);         
        }

        return $response;

    }

    public function getName() {
        return self::COMMAND_NAME;
    }

} 
