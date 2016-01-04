<?php

/**
 *
 * @version $Id: Acl.php 651 2012-05-22 20:30:59Z fred $
 */
class Model_Mapper_Acl extends App_Model_Mapper_Abstract
{
	/**
	 * 
	 * @access public
	 * @return array
	 */
	public function getResources()
	{
		$frontendOptions = array(
			'lifetime' 					=> 86400, // 24hrs
			'automatic_serialization' 	=> true
		);
		
		$backendOptions = array('cache_dir' => APPLICATION_PATH . '/cache');
		
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		
		//Tipo de usuario
		switch ( Zend_Auth::getInstance()->getIdentity()->usuario_nivel ) {
			
			//Normal
			case 'N':
				$data = $this->_getResourcesByUser( $cache );
				break;
				
			//Gestor
			case 'G':
				$data = $this->_getResourcesByManager( $cache );
				break;
				
			//Administrador
			case 'A':
				$data = $this->_getResourcesByAdmin( $cache );
				break;
			
		}
		
		return $data;
	}
	
	/**
	 * 
	 * @access protected
	 * @param Zend_Cache $cache
	 * @return array
	 */
	protected function _getResourcesByUser ( Zend_Cache_Core $cache )
	{
		$id = 'acl_perfil_' . Zend_Auth::getInstance()->getIdentity()->perfil_id;
		
		if ( false === ($rows = $cache->load($id)) ) {
			
			$dbPerfil 		= App_Model_DbTable_Factory::get('Perfil');
			$dbPermissao 	= App_Model_DbTable_Factory::get('Permissao');
			$dbAcao 		= App_Model_DbTable_Factory::get('Acao');
			$dbPrivilegios 	= App_Model_DbTable_Factory::get('Privilegios');
			$dbTela 		= App_Model_DbTable_Factory::get('Tela');
			$dbModulo 		= App_Model_DbTable_Factory::get('Modulo');
			
			$select = $dbPerfil->select()
				->setIntegrityCheck(false)
				->from(
					array('perfil' => $dbPerfil),
					array()
				)
				->join(
					array('permissao' => $dbPermissao),
					'permissao.perfil_id = perfil.perfil_id',
					array()
				)
				->join(
					array('acao' => $dbAcao),
					'acao.acao_id = permissao.acao_id',
					array()
				)
				->join(
					array('privilegios' => $dbPrivilegios ),
					'privilegios.acao_id = acao.acao_id',
					array('privilege' => 'privilegios_action')
				)
				->join(
					array('tela' => $dbTela),
					'tela.tela_id = acao.tela_id',
					array('resource' => 'tela_path')
				)
				->join(
					array('modulo' => $dbModulo), 
					'modulo.modulo_id = tela.modulo_id',
					array()
				)
				->where('perfil.perfil_id = :perfil_id')
				->where('perfil.perfil_status = :status')
				->where('tela.tela_status = :status')
				->where('modulo.modulo_status = :status')
				->bind(
					array(
						':perfil_id' => Zend_Auth::getInstance()->getIdentity()->perfil_id,
						':status' => 1
					)
				);
				
				$rows = $dbPerfil->fetchAll( $select );
				
				$cache->save($rows, $id, array('acl'));
		
		}
		
		$data = array();
		
		if ( $rows->count() ) {
			foreach ( $rows as $row ) 
				$data[$row->resource][] = $row->privilege;
		}
		
		return $data;
	}
	
	/**
	 * 
	 * @access protected
	 * @param Zend_Cache $cache
	 * @return array
	 */
	protected function _getResourcesByManager( Zend_Cache_Core $cache )
	{
		$id = 'acl_usuario_gestor';
		
		if ( false === ($rows = $cache->load($id)) ) {
			
			$dbAcao 		= App_Model_DbTable_Factory::get('Acao');
			$dbPrivilegios          = App_Model_DbTable_Factory::get('Privilegios');
			$dbTela 		= App_Model_DbTable_Factory::get('Tela');
			$dbModulo 		= App_Model_DbTable_Factory::get('Modulo');
			
			$select = $dbAcao->select()
				->setIntegrityCheck(false)
				->from(
					array('acao' => $dbAcao),
					array()
				)
				->join(
					array('tela' => $dbTela),
					'tela.tela_id = acao.tela_id',
					array('resource' => 'tela_path')
				)
				->join(
					array('modulo' => $dbModulo), 
					'modulo.modulo_id = tela.modulo_id',
					array()
				)
				->where('tela.tela_status = :status')
				->where('modulo.modulo_status = :status')
				->bind(
					array(
						':status' => 1
					)
				);
				
				$rows = $dbAcao->fetchAll( $select );
				
				$cache->save($rows, $id, array('acl'));
		
		}
		
		$data = array();
		
		if ( $rows->count() ) {
			foreach ( $rows as $row )
				$data[$row->resource] = null;
		}
		
		return $data;
	}
	
	/**
	 * 
	 * @access protected
	 * @param Zend_Cache_Core $cache
	 * @return array
	 */
	protected function _getResourcesByAdmin( Zend_Cache_Core $cache )
	{
		$id = 'acl_usuario_admin';
		
		if ( false === ($rows = $cache->load($id)) ) {
			
			$dbAcao 		= App_Model_DbTable_Factory::get('Acao');
			$dbPrivilegios 	= App_Model_DbTable_Factory::get('Privilegios');
			$dbTela 		= App_Model_DbTable_Factory::get('Tela');
			$dbModulo 		= App_Model_DbTable_Factory::get('Modulo');
			
			$select = $dbAcao->select()
				->setIntegrityCheck(false)
				->from(
					array('acao' => $dbAcao),
					array()
				)
				->join(
					array('tela' => $dbTela),
					'tela.tela_id = acao.tela_id',
					array('resource' => 'tela_path')
				)
				->join(
					array('modulo' => $dbModulo), 
					'modulo.modulo_id = tela.modulo_id',
					array()
				);
							
				$rows = $dbAcao->fetchAll( $select );
				
				$cache->save($rows, $id, array('acl'));
		
		}
		
		$data = array();
		
		if ( $rows->count() ) {
			foreach ( $rows as $row ) 
				$data[$row->resource] = null;
		}
		
		return $data;
	}
	
	/** 
	 * 
	 * @access public
	 * @return array
	 */
	public function getActionToolbar()
	{
		$frontendOptions = array(
			'lifetime' 					=> 86400, // 24hrs
			'automatic_serialization' 	=> true
		);
	
		$backendOptions = array('cache_dir' => APPLICATION_PATH . '/cache');
	
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		
		//Tipo de usuario
		switch ( Zend_Auth::getInstance()->getIdentity()->usuario_nivel ) {
			
			//Normal
			case 'N':
				$data = $this->_getAccessToolbarByUser( $cache );
				break;
				
			//Gestor
			case 'G':
				$data = $this->_getAccessToolbarByManager( $cache );
				break;
				
			//Administrador
			case 'A':
				$data = $this->_getAccessToolbarByAdmin( $cache );
				break;
			
		}
		
		return $data;
	}

	/**
	 * 
	 * @access protected
	 * @param Zend_Cache_Core $cache
	 * @return array 
	 */
	protected function _getAccessToolbarByUser( Zend_Cache_Core $cache )
	{
		$id = 'acl_toolbar_' . Zend_Auth::getInstance()->getIdentity()->perfil_id;
	
		if ( false === ($data = $cache->load($id)) ) {
		
			$dbPerfil 		= App_Model_DbTable_Factory::get('Perfil');
			$dbPermissao 	= App_Model_DbTable_Factory::get('Permissao');
			$dbAcao 		= App_Model_DbTable_Factory::get('Acao');
			$dbPrivilegios 	= App_Model_DbTable_Factory::get('Privilegios');
			$dbTela 		= App_Model_DbTable_Factory::get('Tela');
			$dbModulo 		= App_Model_DbTable_Factory::get('Modulo');
			
			$select = $dbPerfil->select()
				->setIntegrityCheck(false)
				->from(
					array('perfil' => $dbPerfil),
					array()
				)
				->join(
					array('permissao' => $dbPermissao),
					'permissao.perfil_id = perfil.perfil_id',
					array()
				)
				->join(
					array('acao' => $dbAcao),
					'acao.acao_id = permissao.acao_id',
					array('acao_identificador')
				)
				->join(
					array('privilegios' => $dbPrivilegios ),
					'privilegios.acao_id = acao.acao_id',
					array()
				)
				->join(
					array('tela' => $dbTela),
					'tela.tela_id = acao.tela_id',
					array()
				)
				->join(
					array('modulo' => $dbModulo), 
					'modulo.modulo_id = tela.modulo_id',
					array()
				)
				->where('perfil.perfil_status = :status')
				->where('perfil.perfil_id = :perfil')
				->where('tela.tela_status = :status')
				->where('modulo.modulo_status = :status')
				->bind(
					array(
					    ':status' => 1,
					    ':perfil' => Zend_Auth::getInstance()->getIdentity()->perfil_id
					)
				);
				
			$rows = $dbPerfil->fetchAll( $select );

			$data = array();
			
			foreach ( $rows as $row )
				$data[] = $row->acao_identificador;
						
			$cache->save($data, $id, array('acl'));
	
		}
	
		return $data;
	}
	
	/**
	 * 
	 * @access protected
	 * @param Zend_Cache_Core $cache
	 * @return array
	 */
	protected function _getAccessToolbarByManager( Zend_Cache_Core $cache )
	{
		$id = 'acl_toolbar_usuario_gestor';
	
		if ( false === ($data = $cache->load($id)) ) {
		
			$dbAcao 		= App_Model_DbTable_Factory::get('Acao');
			$dbTela 		= App_Model_DbTable_Factory::get('Tela');
			$dbModulo 		= App_Model_DbTable_Factory::get('Modulo');
			
			$select = $dbAcao->select()
				->setIntegrityCheck(false)
				->from(
					array('acao' => $dbAcao),
					array('acao_identificador')
				)
				->join(
					array('tela' => $dbTela),
					'tela.tela_id = acao.tela_id',
					array()
				)
				->join(
					array('modulo' => $dbModulo), 
					'modulo.modulo_id = tela.modulo_id',
					array()
				)
				->where('tela.tela_status = :status')
				->where('modulo.modulo_status = :status')
				->bind(
					array(
						':status' => 1
					)
				);
				
			$rows = $dbAcao->fetchAll( $select );

			$data = array();
			
			foreach ( $rows as $row )
				$data[] = $row->acao_identificador;
			
			$cache->save($data, $id, array('acl'));
	
		}
	
		return $data;		
	}
	
	/**
	 * 
	 * @access protected
	 * @param Zend_Cache_Core $cache
	 * @return array
	 */
	protected function _getAccessToolbarByAdmin( Zend_Cache_Core $cache )
	{
		$id = 'acl_toolbar_usuario_admin';
	
		if ( false === ($data = $cache->load($id)) ) {
		
			$dbAcao = App_Model_DbTable_Factory::get('Acao');
			
			$select = $dbAcao->select()
				->setIntegrityCheck(false)
				->from(
					array('acao' => $dbAcao),
					array('acao_identificador')
				);
				
			$rows = $dbAcao->fetchAll( $select );

			$data = array();
			
			foreach ( $rows as $row )
				$data[] = $row->acao_identificador;
			
			$cache->save($data, $id, array('acl'));
	
		}
	
		return $data;		
	}
}