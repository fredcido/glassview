<?php

/**
 * 
 * @version $Id: Cheque.php 686 2012-06-03 17:17:57Z ze $
 */
class Model_Mapper_Cheque extends App_Model_Mapper_Abstract
{
    
    public function fetchGrid()
    {

	$dbCheque   = App_Model_DbTable_Factory::get( 'Cheque' );
	$dbConta    = App_Model_DbTable_Factory::get( 'Conta' );
	$dbBanco    = App_Model_DbTable_Factory::get( 'Banco' );
	$dbTerceiro = App_Model_DbTable_Factory::get( 'Terceiro' );

	$select = $dbCheque->select()
		->setIntegrityCheck( false )
		->from(
			array('ch' => $dbCheque), array('ch.*')
		)
		->join(
                    array('ct' => $dbConta), 'ct.fn_conta_id  = ch.fn_conta_id ',
                    array('ct.fn_conta_descricao')
                )
		->join(
                    array('t' => $dbTerceiro), 't.terceiro_id  = ch.terceiro_id ',
                    array('t.terceiro_nome')
                )
		->joinLeft(
		array('b' => $dbBanco), 'b.fn_banco_id  = ct.fn_banco_id ',
                array('b.fn_banco_codigo','b.fn_banco_nome',)
	);

	$rows = $dbCheque->fetchAll( $select );

        $data = array( 'rows' => array() );
        
        $date = new Zend_Date();

	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {
                
                if( empty( $row->fn_banco_codigo ))
                    
                    $banco = '-'; 
                else
                    
                   $banco = $row->fn_banco_codigo .' - '.$row->fn_banco_nome;
                
                if( !empty( $row->fn_cheque_data )){
                    
                    $date->set( $row->fn_cheque_data );
                    $data_emissao = $date->toString( 'dd/MM/yyyy' );
                }else{
                    
                    $data_emissao = '-';
                }
                if( !empty( $row->fn_cheque_para )){
                    
                    $date->set( $row->fn_cheque_para );
                    $data_para = $date->toString( 'dd/MM/yyyy' );
                }else{
                    
                    $data_para = '-';
                }
		$data['rows'][] = array(
		    'id' => $row->fn_cheque_id,
		    'data' => array(
			++$key,
                        $banco,
			$row->fn_conta_descricao,
			$row->fn_cheque_numero,
			$row->terceiro_nome,
			$data_emissao,
			$data_para,
                        $this->_getSistuacaoCheque( $row->fn_cheque_situacao )
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Cheque' );
	    
	    $where = array( 'UPPER(fn_conta_id) = UPPER(?)' => $this->_data['fn_conta_id'],
                            'UPPER(fn_cheque_numero) = UPPER(?)'  => $this->_data['fn_cheque_numero']);
		
	    if ( !$dbTable->isUnique( $where, $this->_data['fn_cheque_id'] ) ) {

                $translate = Zend_Registry::get('Zend_Translate');
                
		$this->_message->addMessage( $translate->_('Cheque já cadastrado.'), App_Message::ERROR );
		return false;
	    }
	    
	    return parent::_simpleSave( $dbTable );

        } catch ( Exception $e ) {

            return false;

        }
    }

    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'fn_cheque_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Cheque' ), $where );
    }
    
    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getSistuacaoCheque( $type )
    {
        $optSituacao = array(
            'A' => 'A Compensar',
            'D' => 'Depositado',
            'C' => 'Compensado',
            'V' => 'Devolvido',
            'P' => 'Pago',
            'R' => 'Repassado'
        );

	return ( empty( $optSituacao[$type] ) ? '-' : $optSituacao[$type] );
    }

	/**
	 * @access public
	 * @return Zend_Db_Table_Rowset
	 */
    public function fetchLancamento()
    {
        $dbCheque           = App_Model_DbTable_Factory::get( 'Cheque' );
        $dbChequeLancamento = App_Model_DbTable_Factory::get( 'ChequeLancamento' );
        $dbLancamento 	    = App_Model_DbTable_Factory::get( 'Lancamento' );
		$dbConta 			= App_Model_DbTable_Factory::get( 'Conta' );
		$dbBanco 			= App_Model_DbTable_Factory::get( 'Banco' );  

		//Ignora cheque que ja esta relacionado com algum lancamentos  
        $selectNotIn = $dbChequeLancamento->select()
            ->setIntegrityCheck( false )
            ->from(
                    array( 'cl' => $dbChequeLancamento ),
                    array( 'fn_cheque_id' )
            )
            ->join(
                    array( 'l' => $dbLancamento ),
                    'l.fn_lancamento_id = cl.fn_lancamento_id',
                    array()
            );
			
		if ( !empty($this->_data['fn_lancamento_id']) )
			$selectNotIn->where( 'l.fn_lancamento_id <> ?', $this->_data['fn_lancamento_id'] );
		
		//Verifica se o cheque tem relacao com o lancamento
		$subSelect = $dbChequeLancamento->select()
			->setIntegrityCheck( false )
			->from(
				array( 'chl' => $dbChequeLancamento ),
				array( new Zend_Db_Expr('COUNT(1)') )
			)
			->join(
				array( 'lancamento' => $dbLancamento ),
				'lancamento.fn_lancamento_id = chl.fn_lancamento_id',
				array()
			)
			->where( 'chl.fn_cheque_id = cq.fn_cheque_id' );

        $select = $dbCheque->select()
			->setIntegrityCheck( false )
            ->from( 
            	array( 'cq' => $dbCheque ),
            	array( 
            		'fn_cheque_id', 
            		'fn_cheque_numero', 
            		'fn_cheque_valor',
            		'selected' => new Zend_Db_Expr( '(' . $subSelect . ')' ) 
				)
			)
			->join(
				array( 'ct' => $dbConta ),
				'ct.fn_conta_id = cq.fn_conta_id',
				array( 'fn_conta_descricao' )
			)
			->joinLeft(
				array( 'b' => $dbBanco ),
				'b.fn_banco_id = ct.fn_banco_id',
				array( 'fn_banco_nome' )
			)
			->where( 'fn_cheque_id NOT IN(?)', $selectNotIn );
			
        $rows = $dbCheque->fetchAll( $select );

        return $rows;
    }

}