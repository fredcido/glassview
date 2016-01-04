<?php

class Relatorio_Model_Mapper_ReconciliacaoCartaoCredito extends App_Model_Mapper_Abstract
{

    /**
     * 
     * @access public
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAll()
    {
	$dbCartaoCredito = App_Model_DbTable_Factory::get( 'CartaoCredito' );
	$dbFatura = App_Model_DbTable_Factory::get( 'Fatura' );
	$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
	$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
	$dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );

	$select = $dbCartaoCredito->select()
		//->distinct()
		->setIntegrityCheck( false )
		->from( array( 'cc' => $dbCartaoCredito ), array( 'fn_cc_descricao' ) )
		->join(
		    array('f' => $dbFatura), 
		    'f.fn_cc_id = cc.fn_cc_id', 
		    array(
			'fn_cc_fat_ref' => new Zend_Db_Expr( "DATE_FORMAT(fn_cc_fat_ref, '%m/%Y')" ),
			'fn_cc_fat_vencimento',
			'fn_cc_fat_total',
			'fn_cc_fat_efetivado'
		    )
		)
		->join(
		    array( 'l' => $dbLancamento ), 
		    'l.fn_lancamento_id = f.fn_lancamento_id', 
		    array( 'fn_lancamento_data' )
		)
		->join(
		    array( 'c' => $dbConta ), 
		    'c.fn_conta_id = l.fn_conta_id', 
		    array( 'fn_conta_descricao' )
		)
		/*
		  ->join(
		  array( 'lf' => $dbLancamentoFatura ),
		  'lf.fn_cc_fat_id = f.fn_cc_fat_id',
		  array()
		  )
		  ->join(
		  array( 'lc' => $dbLancamentoCartao ),
		  'lc.fn_lanc_cartao_id = lf.fn_lanc_cartao_id',
		  array()
		  )
		 */
		->where( 'DATE(f.fn_cc_fat_vencimento) >= ?', $this->_data['dt_start'] )
		->where( 'DATE(f.fn_cc_fat_vencimento) <= ?', $this->_data['dt_end'] );

	//Conta
	if ( !empty( $this->_data['fn_cc_id'] ) )
	    $select->where( 'cc.fn_cc_id = ?', $this->_data['fn_cc_id'] );

	//Efetivado
	if ( array_key_exists( 'faturado', $this->_data ) && '' !== $this->_data['faturado'] )
	    $select->where( 'cc.fn_cc_id = ?', $this->_data['faturado'] );

	$rows = $dbCartaoCredito->fetchAll( $select );

	return $rows;
    }

    /**
     * 
     * @access public
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function reconciliacaoCartaoCredito()
    {
	$rows = $this->fetchAll();

	return $rows;
    }

}