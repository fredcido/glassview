<?php

/**
 * 
 * @version $Id: Lembrete.php 489 2012-04-04 17:53:29Z fred $
 */
class Model_Mapper_Lembrete extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return array 
     */
    public function fetchGrid()
    {
        $rows = $this->listAll();
	
        $data = array( 'rows' => array() );

        if ( $rows->count() ) {

	    $date = new Zend_Date();
	    
	    $translate = Zend_Registry::get('Zend_Translate');
	    
            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->lembrete_id . $row->fluxo,
                    'data'  => array(
                        ++$key,
                        $row->lembrete_titulo,
			$row->lembrete_msg,
                        $translate->_( $row->fluxo == 'E' ? 'Enviado' : 'Recebido' ),
			$translate->_( $row->lembrete_nivel == 1 ? 'Normal' : 'Urgente' ),
                        ( empty( $row->lembrete_data_prevista ) ?  '-' : $date->set( $row->lembrete_data_prevista )->toString('d/MM/Y HH:mm') )
                    )
                );
            }
        }

        return $data;
    }

    /**
     *
     * @return boolean
     */
    public function save()
    {
	$dbLembrete = App_Model_DbTable_Factory::get( 'Lembrete' );
	$dbLembreteDestino = App_Model_DbTable_Factory::get( 'LembreteDestino' );
	
	$dbLembrete->getAdapter()->beginTransaction();
	
        try {
	    
	    $dataForm = $this->_data;
	    
	    if ( !array_key_exists( 'remetente', $this->_data ) )
		$this->_data['remetente'] = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	    
	    if ( empty( $this->_data['lembrete_data_prevista'] ) || empty( $this->_data['lembrete_hora_prevista'] ) )
		unset( $this->_data['lembrete_data_prevista'] );
	    else
		$this->_data['lembrete_data_prevista'] .= ' ' . str_replace( 'T', '', $this->_data['lembrete_hora_prevista'] );
	    
	    // Insere Lembrete
	    $lembrete_id = parent::_simpleSave( $dbLembrete, false );
	    
	    // Busca Usuarios para inserir lembrete
	    $usuarios = $this->getUsersLembreteFiltro( $dataForm );
	    	    
	    // Verifica se foram encontrados usuarios
	    if ( empty( $usuarios ) ) {
		
		$this->_message->addMessage( 'Não foram encontrados usuários para inserir o lembrete.', App_Message::WARNING );
		return false;
	    }
		
	    // Busca Usuarios que ja estao inseridos no lembrete
	    $usuariosRowSet = $dbLembreteDestino->fetchAll( array( 'lembrete_id = ?' => $lembrete_id ) );
	    
	    $usuariosBase = array();
	    foreach ( $usuariosRowSet as $usuario )
		$usuariosBase[] = $usuario->usuario_id;
	    
	    // Novos usuarios a serem inseridos
	    $usuariosNovos = array_diff( $usuarios, $usuariosBase );
	    
	    // Insere novos usuarios para receber o lembrete
	    foreach ( $usuariosNovos as $usuario ) {
		
		$row = $dbLembreteDestino->createRow();
		$row->usuario_id = $usuario;
		$row->lembrete_id = $lembrete_id;
		$row->save();
	    }
	    
	    // Busca usuarios que ainda nao foram disparados
	    $usuariosPendentes = array();
	    foreach ( $usuariosRowSet as $usuario ) 
		if ( empty( $usuario->lembrete_destino_status ) )
		    $usuariosPendentes[] = $usuario->usuario_id;
	    
	    $usuariosDelete = array_diff( $usuariosPendentes, $usuarios );
	    
	    // Remove usuarios que ainda nao foram disparados e nao estao mais marcados para receberem o lembrete
	    if ( !empty( $usuariosDelete ) ) {
	    
		$where = array();
		$where[] = $dbLembreteDestino->getAdapter()->quoteInto( 'usuario_id IN (?)', $usuariosDelete );
		$where[] = $dbLembreteDestino->getAdapter()->quoteInto( 'lembrete_id = ?', $lembrete_id );

		$dbLembreteDestino->delete( $where );
	    }
	    	    
	    $dbLembrete->getAdapter()->commit();
	    
	    return $lembrete_id;

        } catch ( Exception $e ) {
	    
	    $dbLembrete->getAdapter()->rollBack();

            return false;
        }
    }

    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'lembrete_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Lembrete' ), $where );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listAll()
    {
	$dbLembrete = App_Model_DbTable_Factory::get( 'Lembrete' );
	$dbLembreteDestino = App_Model_DbTable_Factory::get( 'LembreteDestino' );
	
	$usr_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	
	$selectEnviados = $dbLembrete->select()
				     ->setIntegrityCheck( false )
				     ->from( array( 'l' => $dbLembrete ), array( '*', 'fluxo' => new Zend_Db_Expr( "'E'" ) ) )
				     ->where( 'l.remetente = ?', $usr_id );
	
	$selectRecebidos = $dbLembrete->select()
				      ->setIntegrityCheck( false )
				      ->from( array( 'l' => $dbLembrete ), array( '*', 'fluxo' => new Zend_Db_Expr( "'R'" ) ) )
				      ->join( 
					 array( 'ld' => $dbLembreteDestino ),
					 'ld.lembrete_id = l.lembrete_id',
					 array()
				      )
				      ->where( 'ld.usuario_id = ?', $usr_id )
				      ->group( 'l.lembrete_id' );
	
	$select = $dbLembrete->select()
			     ->union( array( $selectEnviados, $selectRecebidos ) )
			     ->order( 'lembrete_data DESC' );
		
	return $dbLembrete->fetchAll( $select );
    }
    
    /**
     *
     * @param array $filtro
     * @return array 
     */
    public function getUsersLembreteFiltro( $filtro )
    {
	$usuarios = array();
	
	// Pega usuarios enviados
	if ( !empty( $filtro['usuarios'] ) )
	    $usuarios = array_merge( $filtro['usuarios'], $usuarios );
	
	// Busca Usuarios dos perfis definidos
	if ( !empty( $filtro['perfis'] ) ) {
	    
	    $dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	    $usuarioRowSet = $dbUsuario->fetchAll( array( 'perfil_id IN (?)' => $filtro['perfis'] ) );
	    
	    foreach ( $usuarioRowSet as $usuario )
		$usuarios[] = $usuario->usuario_id;
	}
	
	// Busca Usuarios dos niveis definidos
	if ( !empty( $filtro['nivel'] ) ) {
	    
	    $dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	    $usuarioRowSet = $dbUsuario->fetchAll( array( 'usuario_nivel IN (?)' => $filtro['nivel'] ) );
	    
	    foreach ( $usuarioRowSet as $usuario )
		$usuarios[] = $usuario->usuario_id;
	}
	   
	return array_unique( $usuarios );
    }
    
    /**
     *
     * @param int $usr_id
     * @return Zend_Db_Table_Rowset
     */
    public function buscaLembretesUser( $usr_id )
    {
	$dbLembrete = App_Model_DbTable_Factory::get( 'Lembrete' );
	$dbLembreteDestino = App_Model_DbTable_Factory::get( 'LembreteDestino' );
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	
	$select = $dbLembrete->select()
			     ->setIntegrityCheck( false )
			     ->from( array( 'l' => $dbLembrete ) )
			     ->join(
				array( 'ld' => $dbLembreteDestino ),
				'ld.lembrete_id = l.lembrete_id',
				array( )
			     )
			     ->joinLeft(
				array( 'u' => $dbUsuario ),
				'u.usuario_id = l.remetente',
				array( 'usuario' => 'usuario_nome' )
			     )
			     ->where( 'ld.lembrete_destino_status = ?', 0 )
			     ->where( 'ld.usuario_id = ?', $usr_id )
			     ->where( ' ( l.lembrete_data_prevista <= ?', Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss') )
			     ->orWhere( 'l.lembrete_data_prevista IS NULL ) ' )
			     ->order( array( 'l.lembrete_nivel', 'l.lembrete_data' ) );
	
	return $dbLembrete->fetchAll( $select );
    }
    
    /**
     *
     * @param int $id
     * @return App_Model_DbTable_Row_Abstract
     */
    public function detalhaLembrete( $id )
    {
	$dbLembrete = App_Model_DbTable_Factory::get( 'Lembrete' );
	$dbLembreteDestino = App_Model_DbTable_Factory::get( 'LembreteDestino' );
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	
	$usr_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	
	$select = $dbLembrete->select()
			     ->setIntegrityCheck( false )
			     ->from( array( 'l' => $dbLembrete ) )
			     ->join(
				array( 'ld' => $dbLembreteDestino ),
				'ld.lembrete_id = l.lembrete_id',
				array( 'lembrete_destino_status' )
			     )
			     ->joinLeft(
				array( 'u' => $dbUsuario ),
				'u.usuario_id = l.remetente',
				array( 'usuario' => 'usuario_nome' )
			     )
			     ->where( 'l.lembrete_id = ?', $id )
			     ->where( 'ld.usuario_id = ?', $usr_id );
	
	return $dbLembrete->fetchRow( $select );
    }
    
    /**
     *
     * @param int $id
     * @return array 
     */
    public function mudarStatus( $id )
    {
	try {
	    
	    $lembrete = $this->detalhaLembrete( $id );
	    
	    $dbLembreteDestino = App_Model_DbTable_Factory::get( 'LembreteDestino' );
	    $usr_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	    
	    $campo = array( 'lembrete_destino_status' => (int)!$lembrete->lembrete_destino_status );
	    
	    $where = array();
	    $where[] = $dbLembreteDestino->getAdapter()->quoteInto( 'usuario_id = ?', $usr_id );
	    $where[] = $dbLembreteDestino->getAdapter()->quoteInto( 'lembrete_id = ?', $id );
	    
	    $dbLembreteDestino->update( $campo, $where );
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    return array( 'status' => false );
	}
    }
}