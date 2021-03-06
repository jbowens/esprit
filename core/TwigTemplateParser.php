<?php

namespace esprit\core;

use \esprit\core\util\Logger as Logger;
use \esprit\core\util\LogEventFactory as LogEventFactory;
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
    protected $twigLoader;

    public function __construct(Config $config, Logger $logger, Translator $translator) {
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

        $templateDirs = array();

        if( isset($twigSettings['templates_dir']) )
            array_push($templateDirs, $twigSettings['templates_dir']);
        array_push($templateDirs, $config->get('esprit_data') . DIRECTORY_SEPARATOR . 'templates');

        $this->twigLoader = new \Twig_Loader_Filesystem( $templateDirs ); 

        $options = array();
        if( isset($twigSettings['options']) && is_array($twigSettings['options']) ) {
            $options = $twigSettings['options'];
        }
        if( $config->settingExists('debug') && $config->get('debug') )
            $options['debug'] = true;
        $this->twig = new \Twig_Environment($this->twigLoader, $options);
    
        $this->twig->addExtension( new TwigExtension( $translator ) );
    }

    /**
     * Checks for the existence of the given template.
     */
    public function templateExists( $template ) {
        $paths = $this->twigLoader->getPaths();

        foreach( $paths as $path ) {
            $fullFilename = $path . DIRECTORY_SEPARATOR . $this->getResourceName( $template );
            if( file_exists( $fullFilename ) )
                return true;
        }

        return false;
    }

    /**
     * Returns the raw text of the evaluated template.
     *
     * @param $template  the template to evaluate
     */
    public function evaluateTemplate( $template ) {
        try {
            $templateFile = $this->getResourceName( $template );
            $temp = $this->twig->loadTemplate($templateFile);
            return $temp->render( $this->getVariables() );
        } catch( \Twig_Error $exception ) {
            $this->logger->log( LogEventFactory::createFromException( $exception, self::LOG_SOURCE ) ); 
        }
    }

    /**
     * See TemplateParser.getResourceName($templateName)
     */
    public function getResourceName( $templateName ) {
        return $templateName . '.' . self::TEMPLATE_EXTENSION;
    }

    /**
     * Adds a template path to search for templates.
     *
     * @param string $path
     */
    public function addTemplatePath( $path ) {
        $this->twigLoader->addPath( $path );
    }

}
