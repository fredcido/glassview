<?php

/**
 * 
 * @version $Id$
 */
class App_Plugins_Acl
{
	/**
	 * 
	 * @var Zend_Acl
	 */
	protected $_acl; 
	
	/**
	 * 
	 * @access public 
	 * @return void
	 */
	public function init()
	{
		$this->_acl = new Zend_Acl();
		
		$this->_addRoles();
		$this->_addResources();
		$this->_addAllowRule();
		
		$this->_saveAcl();
	}
	
	/**
	 * Adiciona os papeis
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _addRoles()
	{
		$this->_acl->addRole( new Zend_Acl_Role('anonymous') );

		if ( null !== ($role = $this->_getRoleUser()) )
			$this->_acl->addRole( new Zend_Acl_Role($role), 'anonymous' );
	}
	
	/**
	 * Adiciona recursos
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _addResources()
	{
		$this->_acl->addResource( new Zend_Acl_Resource('/default/auth/') );
		$this->_acl->addResource( new Zend_Acl_Resource('/default/error/') );
		$this->_acl->addResource( new Zend_Acl_Resource('/default/index/') );
		
		if ( Zend_Auth::getInstance()->hasIdentity() ) {

			$mapper = new Model_Mapper_Acl();
			
			$resources = array_keys( $mapper->getResources() );
            
			foreach ( $resources as $resource ) 
				$this->_acl->addResource( new Zend_Acl_Resource($resource) );
			
		}
	}
	
	/**
	 * Adiciona permissoes
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _addAllowRule()
	{
		$this->_acl->allow( 'anonymous', '/default/auth/', array('index', 'login', 'logout', 'recovery', 'password') );
		$this->_acl->allow( 'anonymous', '/default/error/', array('error') );
		
		$auth = Zend_Auth::getInstance();
		
		if ( $auth->hasIdentity() ) {

			$role = $this->_getRoleUser();
			
			//Area de trabalho
			$this->_acl->allow( $role, '/default/index/', array('index') );
			
			$mapper = new Model_Mapper_Acl();
			
			$resources = $mapper->getResources();
			
			foreach ( $resources as $resource => $privileges ) 
				$this->_acl->allow( $role, $resource, $privileges );
		}
	}
	
	/**
	 * 
	 * 
	 * @access protected
	 * @return string
	 */
	protected function _getRoleUser()
	{
		$auth = Zend_Auth::getInstance();
		
		$role = null;
		
		if ( $auth->hasIdentity() ) {

			switch ( $auth->getIdentity()->usuario_nivel ) {
				
				//Administrador
				case 'A':
					$role = md5('A');
					break;
					
				//Gestor
				case 'G':
					$role = md5('G');
					break;
					
				//Normal
				case 'N':
					$role = $auth->getIdentity()->perfil_nome;
					break;
				
			}
			
		}
		
		return $role;
	}
	
	/**
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _saveAcl()
	{
		$registry = Zend_Registry::getInstance();
		
		$registry->set( 'acl', $this->_acl );
	}
	
	/**
	 * 
	 * @access public
	 * @param string $resource
	 * @param string $privilege
	 * @return boolean
	 */
	public function isAllowed ( $resource, $privilege )
	{
		$auth = Zend_Auth::getInstance();
		
		if ( null === ($role = $this->_getRoleUser()) )
			$role = 'anonymous';
		
		$this->init();
		    	
    	if ( !$this->_acl->has($resource) || !$this->_acl->hasRole($role) || !$this->_acl->isAllowed($role, $resource, $privilege) )
    		return false;
    	else 
    		return true;
	}
	
	/**
	 * 
	 * @access public
	 * @param int $action
	 * @return boolean
	 */
	public function isAllowedToolbar ( $action )
	{
		$mapper = new Model_Mapper_Acl();
		
		$data = $mapper->getActionToolbar();
		
		return in_array($action, $data);
	}
	
	/**
	 * 
	 * @access public
	 * @static
	 * @param string $tela_path
	 * @param string $action
	 * @return null|string
	 */
	public static function getIdentifier( $tela_path, $action )
	{
		$tela_path = strtolower( $tela_path );
		$action = strtolower( $action );
		$action = preg_replace( '/[^a-z0-9]/i', '_', $action );
		
		if ( preg_match( '/^\/(([\w-]+)|([\w-]+)\/([\w-]+))\/$/i', $tela_path, $match ) ) {
		    		    
		    if ( count( $match ) > 4 ) {

			$modulo = preg_replace( '/[^a-z0-9]/i', '_', $match[3] );
			$controller = preg_replace( '/[^a-z0-9]/i', '_', $match[4] );

		    } else {

			$modulo = 'default';
			$controller = preg_replace( '/[^a-z0-9]/i', '_', $match[1] );
		    }	

		    $identifier = array( $modulo, $controller, $action );

		    return implode( '_', $identifier );
			
		}
		
		return null;
	}
}