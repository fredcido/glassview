<?php

/**
 * 
 * @version $Id$
 */
class App_Plugins_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * @var Zend_Controller_Request_Abstract 
     */
    protected $_request;
    
    /**
     *
     * @var Zend_Auth
     */
    protected $_auth;
    
    /**
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     *
     * @var array
     */
    protected $_noAuth = array(
		'module' 		=> 'default',
		'controller' 	=> 'auth',
		'action' 		=> 'index'
    );
    
    /**
     *
     * @var array
     */    
    protected $_noAllowed = array(
    	'module' 		=> 'default',
    	'controller' 	=> 'error',
    	'action' 		=> 'forbidden'
    );
    
    /**
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
		$this->_auth 	= Zend_Auth::getInstance();
		$this->_config 	= Zend_Registry::get('config');
		
		$session = 'Auth_' . ucfirst($this->_config->geral->appid);
		
		$this->_auth->setStorage( new Zend_Auth_Storage_Session($session) );
    }
    
    /**
     *
     * @access public
     * @param Zend_Controller_Request_Abstract $request
     * @return mixed 
     */
    public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
    {
		$this->_request = $request;
		
		switch ( true ) {

			case $this->_checkRoute( 'auth', 'default' ):
				return true;
				break;
				
		    case !$this->_auth->hasIdentity():
			
		    	//Nome do cookie
				$name = 'auth_' . $this->_config->geral->appid;
				
				if ( isset( $_COOKIE[$name] ) ) {
					
					$mapper = new Default_Model_Mapper_Auth();
					
					if ( !$mapper->loginCookie($_COOKIE[$name]) ) 
						$this->_routeNoAuth();
	
				} else $this->_routeNoAuth();
			
				break;
				
		    case $this->_auth->hasIdentity():
		    	
				$resource 	= ('/' . $request->getModuleName() . '/' . $request->getControllerName() . '/');
		    	$privilege 	= $request->getActionName();
		    	
		    	$session = new Zend_Session_Namespace( $this->_config->geral->appid );
		    	
	    		if ( $session->acl->isAllowed($resource, $privilege) ) {
	    			
			    	$route = array(
			    		'module' 		=> $request->getModuleName(),
			    		'controller' 	=> $request->getControllerName(),
			    		'action' 		=> $request->getActionName()
			    	);
			    	
			    	$this->_setRoute( $route );
			    	
	    		} else {
	    			
	    			$this->_setRoute( $this->_noAllowed );
	    			
	    		}
		    	
		    	break;
		}
    }
    
    /**
     * 
     * @access protected
     * @return void
     */
    protected function _routeNoAuth()
    {
		$path = $this->_request->getRequestUri();
		
		$session = new Zend_Session_Namespace( $this->_config->geral->appid );
			
		$this->_setRoute( $this->_noAuth );
    }
    
    /**
     * 
     * @access protected
     * @param array $route
     */
    protected function _setRoute( array $route )
    {
    	$this->_request->setModuleName( $route['module'] );
		$this->_request->setControllerName( $route['controller'] );
		$this->_request->setActionName( $route['action'] );
    }
    
    /**
     * 
     * @access protected
     * @param string $controller
     * @param string|null $module
     * @param string|null $action
     * @return boolean 
     */
    protected function _checkRoute( $controller, $module = null, $action = null )
    {
		$valid = $controller == $this->_request->getControllerName();
			
		if ( $module )
		    $valid = $valid && $module == $this->_request->getModuleName();
		
		if ( $action )
		    $valid = $valid && $action == $this->_request->getActionName();
		
		return $valid;
    }
}