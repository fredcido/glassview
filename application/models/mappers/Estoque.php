<?php

/**
 * 
 * @version $Id: Estoque.php 260 2012-02-15 17:58:40Z fred $
 */
class Model_Mapper_Estoque extends App_Model_Mapper_Abstract
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

	    $currency = new Zend_Currency();
	    $date = new Zend_Date();
	    
	    $translate = Zend_Registry::get('Zend_Translate');
	    
            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->estoque_id . ( $row->estoque_fluxo == 'N' ? '' : 'A' ),
                    'data'  => array(
                        ++$key,
                        $row->produto_descricao,
                        $translate->_( $row->estoque_tipo == 'E' ? 'Entrada' : 'Saída' ),
                        $translate->_( $row->estoque_fluxo == 'N' ? 'Normal' : 'Ajuste' ),
                        $date->set( $row->estoque_data )->toString('d/MM/Y H:m:s'),
			$row->estoque_quantidade,
			$currency->setValue( $row->estoque_valor_atual )->toString(),
                        $currency->setValue( $row->estoque_valor_total )->toString()
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
        try {
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Estoque' );
	    $mapperProduto = new Model_Mapper_Produto();
	    
	    $produto = $mapperProduto->getDadosMovimentacao( $this->_data['produto_id'] );
	    
	    $this->_data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	    
	    if ( !empty( $this->_data['estoque_anterior'] ) )
		$this->_data['estoque_fluxo'] = 'A';
	    
	    if ( 'E' == $this->_data['estoque_tipo'] ) {
		
		$qtde_atual = $this->_data['estoque_qtde_anterior'] + $this->_data['estoque_quantidade'];
		
		$produto->estoque_valor_atual = $qtde_atual;
		
		if ( !empty( $produto->produto_estoque_max ) && $qtde_atual > $produto->produto_estoque_max ) {
		    
		    $this->_message->addMessage( 'Opera&ccedil;&atilde;o ultrapassa valor m&aacute;ximo do estoque.', App_Message::ERROR );
		    return false;
		
		// Avisa se atingiu estoque maximo
		} else if ( !empty( $produto->produto_estoque_max ) && $qtde_atual >= $produto->produto_estoque_max && $produto->produto_aviso )
		    $this->_lembreteEstoque( $produto, 1 );
		
	    } else {
		
		$qtde_atual = $this->_data['estoque_qtde_anterior'] - $this->_data['estoque_quantidade'];
		
		$produto->estoque_valor_atual = $qtde_atual;
		
		if ( !empty( $produto->produto_estoque_min ) && $qtde_atual < $produto->produto_estoque_min && $produto->produto_aviso ) {
		    
		    $this->_message->addMessage( 'Opera&ccedil;&atilde;o ultrapassa valor m&iacute;nimo do estoque.', App_Message::ERROR );
		    return false;
		    
		// Avisa se atingiu estoque minimo
		} else if ( !empty( $produto->produto_estoque_min ) && $qtde_atual <= $produto->produto_estoque_min )
		    $this->_lembreteEstoque( $produto, 0 );
	    }
	    
	    $this->_data['estoque_qtde_atual'] = $qtde_atual;
	    
	    return parent::_simpleSave( $dbTable );

        } catch ( Exception $e ) {
	    
            return false;
        }
    }
    
    /**
     *
     * @param Zend_Db_Table_Row $produto
     * @param int $nivel 
     */
    protected function _lembreteEstoque( $produto, $nivel )
    {
	/*
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	$dbPermissao = App_Model_DbTable_Factory::get( 'Permissao' );
	$dbAcao = App_Model_DbTable_Factory::get( 'Acao' );
	$dbPerfil = App_Model_DbTable_Factory::get( 'Perfil' );
	
	// Busca se o perfil tem permissao de Salvar ou Ajusta o estoque
	$selectPermissao = $dbPermissao->select()
					->from( array( 'p' => $dbPermissao ), new Zend_Db_Expr( 'NULL' ) )
		    			->setIntegrityCheck( false )
					->join(
					    array( 'a' => $dbAcao ),
					    'a.acao_id = p.acao_id',
					    array()
					)
					->where( 'p.perfil_id = u.perfil_id' )
					->where( '( a.acao_identificador = ?', App_Plugins_Acl::getIdentifier( '/almoxarifado/estoque/', 'Salvar' ) )
					->orWhere( 'a.acao_identificador = ? )', App_Plugins_Acl::getIdentifier( '/almoxarifado/estoque/', 'Ajustar' ) );
	
	// Busca usuarios para inserir
	$select = $dbUsuario->select()
			    ->from( array( 'u' => $dbUsuario ), array( 'usuario_id' ) )
			    ->setIntegrityCheck( false )
			    ->join(
				array( 'pf' => $dbPerfil ),
				'pf.perfil_id = u.perfil_id AND pf.perfil_status = 1',
				array()
			    )
			    ->where( 'u.usuario_status = ?', 1 )
			    ->where( 'u.usuario_nivel = ?', 'G' )
			    ->orWhere( '( u.usuario_nivel = ?', 'N' )
			    ->where( 'u.perfil_id IS NOT NULL' )
			    ->where( 'EXISTS (?) )', new Zend_Db_Expr( $selectPermissao ) );
	 */
	
	// Busca perfis para avisar
	$lembreteConfigMapper = new Model_Mapper_LembreteConfig();
	$perfis = $lembreteConfigMapper->listPerfis( 'E' );
	
	if ( empty ( $perfis ) )
	    return;
	
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	$dbPerfil = App_Model_DbTable_Factory::get( 'Perfil' );
	
	// Busca usuarios para inserir
	$select = $dbUsuario->select()
			    ->from( array( 'u' => $dbUsuario ), array( 'usuario_id' ) )
			    ->setIntegrityCheck( false )
			    ->join(
				array( 'pf' => $dbPerfil ),
				'pf.perfil_id = u.perfil_id AND pf.perfil_status = 1',
				array()
			    )
			    ->where( 'u.usuario_status = ?', 1 )
			    ->where( '( u.usuario_nivel = ?', 'G' )
			    ->orWhere( 'u.usuario_nivel = ? )', 'N' )
			    ->where( 'u.perfil_id IN (?)', $perfis );
	
	$data =  $dbUsuario->fetchAll( $select );
	
	$usuarios = array();
	foreach ( $data as $row )
	    $usuarios[] = $row->usuario_id;
	
	$translate = Zend_Registry::get( 'Zend_Translate' );
	
	// Produto atingiu estoque minimo
	if ( $nivel == 0 ) {
	    
	    $dataLembrete = array(
		'lembrete_titulo'   => $translate->_( 'Aviso de estoque mínimo' ),
		'lembrete_msg'	    => sprintf( $translate->_( 'Produto %s atingiu estoque mínimo de %s, quantidade atual: %s' ), 
						$produto->produto_descricao,
						$produto->produto_estoque_min,
						$produto->estoque_valor_atual )
	    );
	
	// Produto atingiu estoque maximo
	} else {
	    
	    $dataLembrete = array(
		'lembrete_titulo'   => $translate->_( 'Aviso de estoque máximo' ),
		'lembrete_msg'	    => sprintf( $translate->_( 'Produto %s atingiu estoque máximo de %s, quantidade atual: %s' ), 
						$produto->produto_descricao,
						$produto->produto_estoque_max,
						$produto->estoque_valor_atual )
	    );
	    
	}
	
	$dataLembrete['usuarios'] = $usuarios;
	$dataLembrete['remetente'] = null;
	
	// Insere lembrete
	$mapperLembrete = new Model_Mapper_Lembrete();
	$mapperLembrete->setData( $dataLembrete )->save();
    }

    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'estoque_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Estoque' ), $where );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listAll()
    {
	$dbEstoque = App_Model_DbTable_Factory::get( 'Estoque' );
	$dbProduto = App_Model_DbTable_Factory::get( 'Produto' );
	
	$select = $dbEstoque->select()
			    ->setIntegrityCheck( false )
			    ->from( array ( 'e' => $dbEstoque ) )
			    ->join(
				array( 'p' => $dbProduto ),
				'p.produto_id = e.produto_id',
				array( 'produto_descricao' )
			    )
			    ->order( 'estoque_data DESC' );
	
	return $dbEstoque->fetchAll( $select );
    }
    
    /**
     *
     * @param int $id
     * @return App_Model_DbTable_Row_Abstract
     */
    public function buscaLancamento( $id )
    {
	$dbEstoque = App_Model_DbTable_Factory::get( 'Estoque' );
	$dbProduto = App_Model_DbTable_Factory::get( 'Produto' );
	
	$select = $dbEstoque->select()
			    ->setIntegrityCheck( false )
			    ->from( array ( 'e' => $dbEstoque ) )
			    ->join(
				array( 'p' => $dbProduto ),
				'p.produto_id = e.produto_id',
				array( 'produto_estoque_min', 'produto_estoque_max' )
			    )
			    ->where( 'e.estoque_id = ?', $id );
	
	return $dbEstoque->fetchRow( $select );
    }
}