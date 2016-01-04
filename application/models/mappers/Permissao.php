<?php

/**
 * 
 * @version $Id: Permissao.php 290 2012-02-19 19:52:44Z fred $
 */
class Model_Mapper_Permissao extends App_Model_Mapper_Abstract
{
    
    /**
     *
     * @return array 
     */
    public function listTelasAcao( $perfil_id )
    {
	$data = array();
	
	$dbTela = App_Model_DbTable_Factory::get( 'Tela' );
	$dbModulo = App_Model_DbTable_Factory::get( 'Modulo' );
	$dbAcao = App_Model_DbTable_Factory::get( 'Acao' );
	$dbPermissao = App_Model_DbTable_Factory::get( 'Permissao' );
	
	$modulos = $dbModulo->fetchAll( array( 'modulo_status = ?' => 1 ), array( 'modulo_nome' ) );
	
	foreach ( $modulos as $modulo ) {
	
	    $telas = $dbTela->fetchAll( array( 'modulo_id = ?' => $modulo->modulo_id, 'tela_status = ?' => 1 ), array( 'tela_nome' ) );

	    // Select de acoes da tela
	    $selectAcoes = $dbAcao->select()
				  ->from( array( 'a' => $dbAcao ) )
				  ->setIntegrityCheck( false )
				  ->joinLeft(
				     array( 'p' => $dbPermissao ),
				     'p.acao_id = a.acao_id AND p.perfil_id = :perfil',
				     array( 'perfil_id' )
				  )
				 ->where( 'a.tela_id = :tela' )
				 ->order( 'acao_descricao' );
	    
	    $childrenTelas = array();

	    foreach ( $telas as $tela ) {

		$selectAcoes->bind(
		    array(
			':perfil' => $perfil_id,
			':tela'	  => $tela->tela_id
		    )
		);

		$acoes = $dbAcao->fetchAll( $selectAcoes );

		$children = array();
		foreach ( $acoes as $acao ) {

		    $children[] = array(
			'id'	=> $acao->acao_id,
			'type'	=> 'child',
			'name'	=> $acao->acao_descricao,
			'checked'	=> empty( $acao->perfil_id ) ? false : true
		    );
		}

		if ( !empty( $children ) ) {
		    
		    $childrenTelas[] = array(
			'id'	   => 'T' . $tela->tela_id,
			'type'	   => 'parent',
			'name'	   => $tela->tela_nome,
			'children'	   => $children
		    );
		}
	    }
	    
	    if ( !empty( $childrenTelas ) ) {
		
		$data[] = array(
		    'id'	   => 'M' . $modulo->modulo_id,
		    'type'	   => 'root',
		    'name'	   => $modulo->modulo_nome,
		    'children' => $childrenTelas
		);
	    }
	}
		
	return $data;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function removerPermissao( $data )
    {
	$dbPermissao = App_Model_DbTable_Factory::get( 'Permissao' );
	
	$dbPermissao->getAdapter()->beginTransaction();
	
	try {
	    
	    if ( empty( $data['acao_id'] ) || empty( $data['perfil_id'] ) )
		return array( 'status' => false, 'message' => $this->_config->messages->error );
	    
	    $where = array();
	    $where[] = $dbPermissao->getAdapter()->quoteInto( 'acao_id = ?', $data['acao_id'] );
	    $where[] = $dbPermissao->getAdapter()->quoteInto( 'perfil_id = ?', $data['perfil_id'] );
	    
	    $dbPermissao->delete( $where );
	    
	    parent::_cleanCacheTag( array( 'acl' ) );
	    
	    $dbPermissao->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbPermissao->getAdapter()->rollBack();
	    
	    return array( 'status' => false, 'message' => $this->_config->messages->error );
	}
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function adicionarPermissao()
    {
	$dbPermissao = App_Model_DbTable_Factory::get( 'Permissao' );
	
	$dbPermissao->getAdapter()->beginTransaction();
	
	try {
	    
	    $row = $dbPermissao->createRow();
	    $row->setFromArray( $this->_data );
	    
	    if ( false !== $row->save() )
		parent::_cleanCacheTag( array( 'acl' ) );
	    
	    $dbPermissao->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbPermissao->getAdapter()->rollBack();
	    
	    return array( 'status' => false, 'message' => $this->_config->messages->error );
	}
    }
}