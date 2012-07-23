<?php

namespace esprit\core\debug;

use \esprit\core\Controller;

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
        parent::setupResolvers();

        // Add debug resolvers
        $this->appendCommandResolver( new DebugCommandResolver( $this ) );
        $this->appendViewResolver( new DebugViewResolver() );
    }

}
