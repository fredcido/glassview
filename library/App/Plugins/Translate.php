<?php

/**
 *
 * @version $Id $
 */
class App_Plugins_Translate extends Zend_Controller_Plugin_Abstract 
{
    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
    {
        $auth = Zend_Auth::getInstance();
        
        if ( $auth->hasIdentity() && !empty($auth->getIdentity()->linguagem_local) ) {
            
            $locale = $auth->getIdentity()->linguagem_local;
            
            $id = 'Zend_Translate_' . $locale;
            
        } else {
            
            $config = Zend_Registry::get( 'config' );
            
            $locale = $config->language->locale;
            $id = 'Zend_Translate_' . $config->language->locale;
            
        }
        
        $frontendOptions = array(
            'lifetime'                  => 86400, // 24hrs
            'automatic_serialization'   => true
        );

        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/cache');

        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        
        if ( false === ($data = $cache->load($id)) ) {
            
            $mapper = new Model_Mapper_Traducao();
            
            $data = $mapper->getTranslate($locale);

            $cache->save($data, $id, array('translate'));

        }

        $translate = new Zend_Translate( 
            'array',
            $data,
            $locale ,
            array( 'disableNotices' => true )
        );
        

        $translate->setLocale( $locale );
        
	$registry = Zend_Registry::getInstance();
        
	$registry->set( 'Zend_Translate' , $translate );

	Zend_Validate_Abstract::setDefaultTranslator( $translate );

        Zend_Form::setDefaultTranslator( $translate );
	
	$objLocale = new Zend_Locale( 'pt_BR' );
	Zend_Registry::set( 'Zend_Locale', $objLocale );
    }
}