<?php

namespace esprit\core;

/**
 * Requests to multiple websites at multiple domains may be routed through the same
 * Esprit installation. The site class may be used to deferentiate between different
 * websites using the same code base. It's particularly useful for creating language-
 * specific or region-specific versions of a website.
 *
 * @author jbowens
 */
class Site {

    protected $siteid;
    protected $domain;
    protected $language;

    public function __construct($siteid, $domain, Language $language) {
        $this->siteid = $siteid;
        $this->domain = $domain;
        $this->language = $language;
    }

    public function getSiteId() {
        return $this->siteid;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function getLanguage() {
        return $this->language;
    }

}
