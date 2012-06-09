<?php

namespace esprit\core;

/**
 * 
 * Stores installation-specific configuration options for the framework. These options are
 * initially loaded from config.xml. This should not be used for storing session or request
 * specific values. Values stored in a Config object should persist acorss the installation
 * acorss all requests and sessions.
 * 
 * @author jbowens
 *
 */
class Config {

	/* The map of configuration options and their values */
	protected $options;
	
	/**
	 * Creates a new configuration object from the given XML file.
	 * 
	 * @param string $configFile  the filename of the file to load
	 * 
	 * @return Config a config object
	 */
	public static function createFromXMLFile($configFile) {
		
		$config = new Config();
		
		$xml = simplexml_load_file( $configFile );
	
		foreach($xml->children() as $option) {
			$config->set($option->getName(), (String) $option);
		}
		
		return $config;
		
	}
	
	/**
	 * Creates a new config instance.
	 */
	protected function __construct() {
		$this->options = array();
	}
	
	/**
	 * Sets a configuration option.
	 * 
	 * @param String $key  the key for the property
	 * @param $val         the value of the property
	 */
	public function set($key, $val) {
		$this->options[$key] = $val;
	}
	
	/**
	 * 
	 * Retrieves the value of the config option with the given key.
	 * 
	 * @param string $key  the key to lookup
	 * 
	 * @return the key's value, or null if the key isn't set
	 */
	public function get($key) {
		if( ! $this->settingExists($key) )
			return null;
		else
			return $this->options[$key];
	}
	
	/**
	 * Determines if a given setting exists.
	 * 
	 * @param String $key  the setting to check
	 * 
	 * @return true iff the setting is set
	 */
	public function settingExists($key) {
		return isset($this->options[$key]);
	}
	
}
