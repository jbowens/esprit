<?php

namespace esprit\core;

use \esprit\core\util\Logger as Logger;
use \esprit\core\exceptions\TwigConfigurationException as TwigConfigurationException;

/**
 * A TemplateParser that uses the Twig templating engine for its
 * backend.
 *
 * @author jbowens
 */
class TwigTemplateParser extends TemplateParser {

    const LOG_SOURCE = "TwigTemplateParser";
    const TEMPLATE_EXTENSION = 'html';

    protected $twig;

    public function __construct(Config $config, Logger $logger) {
        parent::__construct($config, $logger);
        
        if( ! $config->settingExists("twig") )
            throw new TwigConfigurationException("Couldn't find twig options in config file.");

        $twigSettings = $config->get("twig");
        if( ! isset($twigSettings['twig_autoloader']) )
            throw new TwigConfigurationException("Twig autoloader is not defined.");
        
        if( ! isset($twigSettings['templates_dir']) )
            $this->logger->warning(self::LOG_SOURCE, "No twig templates_dir configuration option. Template parser will be using only default esprit templates.");
        elseif( ! is_dir($twigSettings['templates_dir']) )
            $this->logger->error(self::LOG_SOURCE, "Twig templates_dir does not exist or is not a directory.");

        // Include the Twig autoloader
        require_once($twigSettings['twig_autoloader']);
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem( $config->get('esprit_data') . DIRECTORY_SEPARATOR . 'templates' );
        if( isset($twigSetting['templates_dir']) )
            $loader->addPath( $twigSettings['templates_dir'] );

        $options = array();
        if( isset( $twigSettings['cache'] ) )
            $options['cache'] = $twigSettings['cache'];
        $this->twig = new \Twig_Environment($loader, $options);
    }

    public function templateExists( $template ) {
        //TODO: Implement
        return true;
    }

    public function displayTemplate( $template ) {
        $templateFile = $template . '.' . self::TEMPLATE_EXTENSION;
        $temp = $this->twig->loadTemplate($templateFile);
        echo $temp->render( $this->getVariables() );
    }

}
