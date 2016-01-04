<?php

/**
 * 
 */
class App_Application_Resource_Pluginloader extends Zend_Application_Resource_ResourceAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Application_Resource_Resource::init()
	 */
	public function init ()
	{
		$bootstrap = $this->getBootstrap();
		$bootstrap->bootstrap('frontController');
		
		$front = $bootstrap->getResource('frontController');
		
		$loader = new App_Plugins_PluginLoader();
		
		$options = $this->getOptions();
		
		foreach ( $options as $module => $plugins ) {
			foreach ( $plugins as $plugin ) {
				$loader->addPlugin($module, $plugin);
			}
		}
		
		$front->registerPlugin($loader);
	}
}