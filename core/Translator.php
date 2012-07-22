<?php

namespace esprit\core;

/**
 * A translator that will translate to the current locale.
 *
 * @authr jbowens
 */
class Translator {

    const LOG_SOURCE = "TRANSLATOR";

    protected $logger;
    protected $translationSource;
    protected $language;

    public function __construct(util\Logger $logger, TranslationSource $source, $language) {
        $this->logger = $logger;
        $this->translationSource = $source;
        $this->language = $language;
    }

    public function translate( $translationIdentifier ) {

        $this->logger->finest( 'Translating ' . $translationIdentifier . ' into language ' . $this->language, self::LOG_SOURCE );

        try
        {
            return $this->translationSource->getTranslation($translationIdentifier, $this->language);
        }
        catch( exceptions\InvalidTranslationIdentifier $e )
        {
            $this->logger->log( util\LogEventFactory::createFromException( $e, self::LOG_SOURCE) );
            return "";
        }
        catch( exceptions\NonexistentLanguageException $e )
        {
            $this->logger->log( util\LogEventFactory::createFromException( $e, self::LOG_SOURCE) );
            return "";
        }
    }

}
