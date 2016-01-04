<?php

/**
 * 
 * @version $Id: Bootstrap.php 788 2012-07-30 20:44:13Z fred $
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * 
     */
    protected function _initAutoLoader()
    {
	new Zend_Application_Module_Autoloader(
	    array(
		'basePath' 	=> APPLICATION_PATH . '/modules/default',
		'namespace' 	=> 'Default',
		'resourceTypes' => array(
		    'mappers' => array(
			 'path'		=> 'models/mappers',
			 'namespace'	=> 'Model_Mapper'
		    )
		)
	    )
	);
    }

    /**
     * 
     * @access protected
     * @return void
     */
    protected function _initConfig()
    {
	$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini' );

	Zend_Registry::set( 'config', $config );
    }

    /**
     * 
     * @access protected
     * @return void
     */
    protected function _initAcl()
    {
	$acl = new App_Plugins_Acl();
	$acl->init();

	$config = Zend_Registry::get( 'config' );

	$session = new Zend_Session_Namespace( $config->geral->appid );
	$session->acl = $acl;
    }

    /**
     * 
     * @access protected
     * @return void
     */
    protected function _initCacheDir()
    {
	$frontendOptions = array(
	    'lifetime' => 86400,
	    'automatic_serialization' => true,
	    'automatic_cleaning_factor' => 1
	);

	$backendOptions = array( 'cache_dir' => APPLICATION_PATH . '/cache' );

	$cache = Zend_Cache::factory( 'Core', 'File', $frontendOptions, $backendOptions );

	Zend_Db_Table_Abstract::setDefaultMetadataCache( $cache );

	Zend_Date::setOptions( array( 'cache' => $cache ) );
    }
    
    /**
     * 
     */
    public function _initDojoDeclarative()
    {
	$view = $this->bootstrap( 'view')->getResource( 'view' );
	
	Zend_Dojo_View_Helper_Dojo::setUseDeclarative();
        Zend_Dojo::enableView( $view );
    }
}