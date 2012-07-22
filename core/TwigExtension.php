<?php

namespace esprit\core;

class TwigExtension extends \Twig_Extension {

    protected $translator;

    public function __construct(Translator $translator) {
        $this->translator = $translator;
    }

    public function getFunctions() {
        return array(
            't' => new \Twig_Function_Method($this, 'translate'));
    }

    public function translate($identifier) {
        return $this->translator->translate($identifier);
    }

    public function getName() {
        return "esprit";
    }

}
