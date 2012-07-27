<?php

namespace esprit\core\debug;

use \esprit\core\ViewResolver;
use \esprit\core\Response;
use \esprit\core\Config;
use \esprit\core\util\Logger;
use \esprit\core\TemplateParser;

/**
 * A view resolver for debug-mode commands.
 *
 * @author jbowens
 */
class DebugViewResolver implements ViewResolver {

    protected $config;
    protected $logger;
    protected $templateParser;

    public function __construct(Config $config, Logger $logger, TemplateParser $templateParser) {
        $this->config = $config;
        $this->logger = $logger;
        $this->templateParser = $templateParser;
    }

    public function resolve(Response $response) {

        $request = $response->getRequest();
        $url = $request->getUrl();

        if( $url->getPath() == "/TranslationTool" ) {
            require_once "views/View_TranslationTool.php";
            return new views\View_TranslationTool($this->config, $this->logger, $this->templateParser);
        }

        return null;
    }

}
