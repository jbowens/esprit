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

    /**
     * Translates the given translation identifier into this Translator's defined
     * language.
     */
    public function translate( $translationIdentifier ) {

        try
        {
            $translation = $this->translationSource->getTranslation($translationIdentifier, $this->language);
            return $translation;
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

    /**
     * Get the language identifier of the language this Translator translates to.
     */
    public function getLanguageIdentifier() {
        return $this->language;
    }

}
