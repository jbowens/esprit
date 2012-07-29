<?php

namespace esprit\core\debug;

use \esprit\core\Controller;
use \esprit\core\TwigTemplateParser;

/**
 * A DebugController that provides additional debug-mode
 * functionality.
 *
 * @author jbowens
 */
class DebugController extends Controller {

    public function getDatabaseManager() {
        return $this->dbm;
    }

    public function getCache() {
        return $this->cache;
    }

    public function getLanguageSource() {
        return $this->languageSource;
    }

    protected function setupResolvers() {
    
        // Add debug resolvers
        $this->appendCommandResolver( new DebugCommandResolver( $this ) );

        // The debug templates are written in Twig, so we need the TwigTemplateParser, which is not necessarily the
        // one being used elsehwere
        $twigTemplateParser = new TwigTemplateParser($this->config, $this->logger, $this->viewManager->getTranslator() );
        $twigTemplateParser->addTemplatePath( __DIR__ . DIRECTORY_SEPARATOR . 'templates' );
        $this->appendViewResolver( new DebugViewResolver($this->config, $this->logger, $twigTemplateParser) );

        parent::setupResolvers();
   
    }

}
