<?php

namespace esprit\core;

use esprit\core\util\Logger as Logger;

/**
 * The ViewManager handles the presentation of responses. All output should go
 * through this ViewManager.
 *
 * @author jbowens
 */
class ViewManager {

    const LOG_SOURCE = "VIEW_MANAGER";

    protected $config;
    protected $logger;
    protected $cache;
    protected $viewResolvers;
    protected $templateParser;
    protected $translationSource;
    protected $translator;

    public function __construct(Config $config, Logger $logger, TranslationSource $translationSource, Language $language) {
        $this->config = $config;
        $this->logger = $logger;
        $this->viewResolvers = array();

        // TODO: Add support for other template parsers besides Twig
        $this->translationSource = $translationSource;
        $this->translator = new Translator($logger, $translationSource, $language->getIdentifier());
        $this->templateParser = new TwigTemplateParser($config, $logger, $this->translator);
        $this->templateParser->setVariable('ts', $translationSource);
    }

    /**
     * Displays the given response object.
     */
    public function display(Response $response) {

        $view = null;

        // Find the appropriate view
        $i = 0;
        while( $i < count($this->viewResolvers) && $view == null )
        {
            $view = $this->viewResolvers[$i]->resolve($response);
            $i++;
        }

        if( $view == null ) {
            $this->logger->error("No matching view found for " . $response->getRequest()->getUrl()->getPath(), self::LOG_SOURCE, $response);
            $view = new \esprit\core\views\FallbackView($this->config, $this->logger, $this->templateParser);
        }

        $this->logger->finest("Going to use view " . get_class( $view ), self:: LOG_SOURCE);

        $view->display( $response );

    }

    /**
     * Gets the translator used by the view.
     */
    public function getTranslator() {
        return $this->translator;
    }

    /**
     * Adds a view resolver to the list of resolvers used when pairing Response objects with
     * views.
     */
    public function addViewResolver(ViewResolver $viewResolver) {
        array_push($this->viewResolvers, $viewResolver);
    }

    /**
     * Retrieves the template parser used by this view manager.
     */
    public function getTemplateParser() {
        return $this->templateParser;
    }

}
