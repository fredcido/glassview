<?php

/**
 * 
 * @version $Id$
 */
class App_Plugins_Request extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * @var Zend_Controller_Request_Abstract 
     */
    protected $_request;
  
    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
    {
		$this->_request = $request;
	        
		if ( $this->_checkRoute( 'auth', 'default' ) || $this->_checkRoute( 'index', 'default' ) )
		    return true;
		//else if ( !$request->isXmlHttpRequest() )
		//    $this->_routeIndex();
		else
		    Zend_Layout::getMvcInstance()->disableLayout();
    }
    
    /**
     *
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
    
    /**
     * 
     * @access public
     * @return void
     */
    public function _routeIndex()
    {
		$this->_request->setControllerName( 'index' );
		$this->_request->setModuleName( 'default' );
		$this->_request->setActionName( 'index' );
    }
}