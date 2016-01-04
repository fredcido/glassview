<?php

/**
 * 
 * @version $Id: Almoxarifado.php 769 2012-06-22 13:17:01Z ze $
 */
class Relatorio_Model_Mapper_Almoxarifado extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function Estoque()
    {
	$dbEstoque = App_Model_DbTable_Factory::get( 'Estoque' );
	$dbProduto = App_Model_DbTable_Factory::get( 'Produto' );

        $select = $dbEstoque->select()
                        ->from( 
                            array ( 'es' => $dbEstoque ),
                            array(
                                    'es.estoque_data',
                                    'es.estoque_quantidade',
                                    'es.estoque_tipo',
                                    'es.estoque_valor_atual',
                                    'es.estoque_qtde_anterior', 
                                    'es.estoque_qtde_atual'
                                )
                        )
                        ->setIntegrityCheck( false )
                        ->join(
                            array ( 'p' => $dbProduto ),
                            'p.produto_id = es.produto_id',
                            array('p.produto_descricao')
                        )
			->order( array( 'es.estoque_data') );
        
        $select->where( 'es.estoque_fluxo = ?', ( empty( $this->_data['estoque_fluxo'] ) ? 'N' : 'A') );
        
	if ( !empty( $this->_data['produto_id'] ) )
	    $select->where( 'es.produto_id = ?', $this->_data['produto_id'] );
        
	if ( !empty( $this->_data['estoque_tipo'] ) )
	    $select->where( 'es.estoque_tipo = ?', $this->_data['estoque_tipo'] );
        
	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( es.estoque_data ) >= ?', $this->_data['rel_data_ini'] );
	
	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( es.estoque_data ) <= ?', $this->_data['rel_data_fim'] );
        
	return $dbEstoque->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset 
     */
    public function ativo()
    {
	$dbAtivo = App_Model_DbTable_Factory::get( 'Ativo' );
	$dbSituacaoAtivo = App_Model_DbTable_Factory::get( 'SituacaoAtivo' );
	$dbTipoAtivo = App_Model_DbTable_Factory::get( 'TipoAtivo' );
	$dbFilial = App_Model_DbTable_Factory::get( 'Filial' );
	
	$select = $dbAtivo->select()
			  ->setIntegrityCheck( false )
			  ->from(
			      array( 'a' => $dbAtivo ),
			      array( 
				  'ativo_nome', 
				  'ativo_valor', 
				  'ativo_patrimonio', 
				  'ativo_aquisicao', 
				  'ativo_status' 
			      )
			  )
			  ->join(
			    array( 'sa' => $dbSituacaoAtivo ),
			    'sa.situacao_ativo_id = a.situacao_ativo_id',
			    array( 'situacao_ativo_nome' )
			  )
			  ->join(
			    array( 'ta' => $dbTipoAtivo ),
			    'ta.tipo_ativo_id = a.tipo_ativo_id',
			    array( 'tipo_ativo_nome' )
			  )
			  ->join(
			    array( 'f' => $dbFilial ),
			    'f.filial_id = a.filial_id',
			    array( 'filial_nome' )
			  )
			  ->order( 'a.ativo_aquisicao DESC' );
	
	// Filtra por situacao do ativo
	if ( !empty( $this->_data['situacao_ativo_id'] ) )
	    $select->where( 'a.situacao_ativo_id = ?', $this->_data['situacao_ativo_id'] );
	
	// Filtra por tipo do ativo
	if ( !empty( $this->_data['tipo_ativo_id'] ) )
	    $select->where( 'a.tipo_ativo_id = ?', $this->_data['tipo_ativo_id'] );
	
	// Filtra por filial
	if ( !empty( $this->_data['filial_id'] ) )
	    $select->where( 'a.filial_id = ?', $this->_data['filial_id'] );
	
	// Filtra por status
	if ( array_key_exists( 'ativo_status', $this->_data ) && is_numeric( $this->_data['ativo_status'] ) )
	    $select->where( 'a.ativo_status = ?', (int)$this->_data['ativo_status'] );
	
	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( a.ativo_aquisicao ) >= ?', $this->_data['rel_data_ini'] );
	
	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( a.ativo_aquisicao ) <= ?', $this->_data['rel_data_fim'] );
	
	return $dbAtivo->fetchAll( $select );
    }
    
}