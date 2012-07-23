<?php

namespace esprit\core\debug;

use \esprit\core\ViewResolver;
use \esprit\core\Response;

/**
 * A view resolver for debug-mode commands.
 *
 * @author jbowens
 */
class DebugViewResolver implements ViewResolver {

    public function resolve(Response $response) {

        return null;
    }

}
