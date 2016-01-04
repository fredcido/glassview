<?php

/**
 *
 * @version $Id $
 */
class Model_Mapper_Lancamento extends App_Model_Mapper_Abstract
{

    /**
     * 
     * @access public
     * @return mixed
     */
    public function save()
    {
	try {

	    parent::setValidators(
		    array(
			'_validLancamentoProjeto',
			'validaDocumentoFiscal',
			'_validLancamentoCheque'
		    )
	    );
            
	    if ( parent::isValid() ) {
                
                $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
		
                if(!empty($this->_data['lancamento_duplicata_id'])){

                    $lancamentoDuplicataTotal = $this->_data;
		    
                    $where = array( 'fn_lancamento_id = ?' => $this->_data['lancamento_duplicata_id'] );

                    $lancamentoDuplicata = parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Lancamento' ), $where )->toArray();

                    $this->_data = $lancamentoDuplicata;
                    $this->_data['fn_conta_id'] = $lancamentoDuplicataTotal['fn_conta_id'];
                    
                    if ( !empty( $lancamentoDuplicataTotal['fn_lancamento_efetivado'] ) ){
                        
                        $this->_data['fn_lancamento_efetivado'] = 1;
                        $this->_data['fn_lancamento_dtefetivado'] = !empty( $lancamentoDuplicataTotal['fn_lancamento_dtefetivado'] ) ? 
								    $lancamentoDuplicataTotal['fn_lancamento_dtefetivado'] : 
								    Zend_Date::now()->toString( 'yyyy-MM-dd HH:mm:ss' );
                        $this->_efetivar();
                    }
                    
                    $lancamentoDuplicata['fn_conta_id']               = $this->_data['fn_conta_id'];
                    $lancamentoDuplicata['fn_lancamento_efetivado']   = $this->_data['fn_lancamento_efetivado'];
                    $lancamentoDuplicata['fn_lancamento_dtefetivado'] = $this->_data['fn_lancamento_dtefetivado'];
                    
                    $whereUpdate = $dbLancamento->getAdapter()->quoteInto( 'fn_lancamento_id = ?', $lancamentoDuplicata['fn_lancamento_id'] );
                    $dbLancamento->update( $lancamentoDuplicata, $whereUpdate );

                    $this->_data = $lancamentoDuplicataTotal;
                    unset($this->_data['fn_conta_id']);
                    $this->_data['fn_lancamento_efetivado'] = 0;
                }

		if ( empty( $this->_data['fn_lancamento_id'] ) )
		    $this->_data['fn_lancamento_data'] = Zend_Date::now()->toString( 'yyyy-MM-dd HH:mm:ss' );

		if ( !empty( $this->_data['fn_lancamento_efetivado'] ) )
		    $this->_efetivar();
                
		//Salva lancamento
		$fn_lancamento_id = parent::_simpleSave( $dbLancamento, false );
		
		//Salva lancamento por projeto
		$this->saveTipoLancamentoProjeto( $fn_lancamento_id );

		//Salva lancamento de cheques
		$this->_saveLancamentoCheque( $fn_lancamento_id );

		$this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );

		return $fn_lancamento_id;
	    }
	    
	} catch ( Exception $e ) {

            var_dump($e);
            
	    //$this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	}

	return false;
    }

    /**
     * 
     * @access protected
     * @param int $fn_lancamento_id
     * @return void
     */
    public function saveTipoLancamentoProjeto( $fn_lancamento_id )
    {
	$dbLancamentoProjeto = App_Model_DbTable_Factory::get( 'LancamentoProjeto' );

	$total = count( $this->_data['projeto_id'] );

	for ( $i = 0; $i < $total; $i++ ) {

	    $select = $dbLancamentoProjeto->select()
                                            ->from(
                                                    $dbLancamentoProjeto, 
                                                    array('count' => new Zend_Db_Expr( 'COUNT(1)' ))
                                            )
                                            ->where( 'fn_lancamento_id = ?', $fn_lancamento_id )
                                            ->where( 'fn_tipo_lanc_id = ?' , (int)$this->_data['fn_tipo_lanc_id'][$i] )
                                            ->where( 'projeto_id = ?'      , (int)$this->_data['projeto_id'][$i] );

	    $row = $dbLancamentoProjeto->fetchRow( $select );

	    if ( empty( $row->count ) ) {

                if(!empty($this->_data['fn_lanc_projeto_valor'][$i])){
                    
                    //Adiciona
                    $data = array(
                        'fn_lancamento_id'      => $fn_lancamento_id,
                        'fn_tipo_lanc_id'       => (int)$this->_data['fn_tipo_lanc_id'][$i],
                        'projeto_id'            => (int)$this->_data['projeto_id'][$i],
                        'fn_lanc_projeto_valor' => $this->_data['fn_lanc_projeto_valor'][$i]
                    );

                    $row = $dbLancamentoProjeto->createRow();
                    $row->setFromArray( $data );
                    $row->save();
                }
	    } else {

		//Atualiza
		$data = array('fn_lanc_projeto_valor' => $this->_data['fn_lanc_projeto_valor'][$i]);

		$where = array(
		    'fn_lancamento_id = ?'  => (int)$fn_lancamento_id,
		    'fn_tipo_lanc_id = ?'   => (int)$this->_data['fn_tipo_lanc_id'][$i],
		    'projeto_id = ?'	    => (int)$this->_data['projeto_id'][$i],
		);
		
		$dbLancamentoProjeto->update( $data, $where );
	    }
	}
    }

    /**
     * @access public
     * @param int $fn_lancamento_id 
     * @return bool
     */
    protected function _saveLancamentoCheque( $fn_lancamento_id )
    {
	$dbChequeLancamento = App_Model_DbTable_Factory::get( 'ChequeLancamento' );

	if ( !empty( $this->_data['fn_cheque_id'] ) ) {

	    $select = $dbChequeLancamento->select()
		    ->from(
			    $dbChequeLancamento, array('fn_cheque_id')
		    )
		    ->where( 'fn_lancamento_id = ?', (int)$fn_lancamento_id );

	    $rows = $dbChequeLancamento->fetchAll( $select );

	    if ( $rows->count() ) {

		$data = array();

		foreach ( $rows as $row )
		    $data[] = $row->fn_cheque_id;

		$insert = array_diff( $this->_data['fn_cheque_id'], $data );

		$delete = array_diff( $data, $this->_data['fn_cheque_id'] );

		if ( !empty( $insert ) ) {

		    foreach ( $insert as $fn_cheque_id ) {

			$data = array(
			    'fn_lancamento_id' => (int)$fn_lancamento_id,
			    'fn_cheque_id'     => $fn_cheque_id
			);

			$dbChequeLancamento->insert( $data );
		    }
		}

		if ( !empty( $delete ) ) {

		    foreach ( $delete as $fn_cheque_id ) {

			$where = array(
			    'fn_lancamento_id = ?' => $fn_lancamento_id,
			    'fn_cheque_id = ?'     => $fn_cheque_id
			);

			$dbChequeLancamento->delete( $where );
		    }
		}
	    } else {

		foreach ( $this->_data['fn_cheque_id'] as $fn_cheque_id ) {

		    $data = array(
			'fn_lancamento_id' => $fn_lancamento_id,
			'fn_cheque_id' => $fn_cheque_id
		    );

		    $dbChequeLancamento->insert( $data );
		}
	    }
	} else {

	    $where = array('fn_lancamento_id = ?' => $fn_lancamento_id);

	    $dbChequeLancamento->delete( $where );
	}
    }

    /**
     *
     * @access protected
     * @return 
     */
    protected function _efetivar()
    {
	parent::setValidators( array('_validConta') );

	if ( parent::isValid() ) {

	    $dbConta = App_Model_DbTable_Factory::get( 'Conta' );

	    $this->_data['fn_lancamento_dtefetivado'] = !empty( $this->_data['fn_lancamento_dtefetivado'] ) ? 
							$this->_data['fn_lancamento_dtefetivado'] :
							Zend_Date::now()->toString( 'yyyy-MM-dd HH:mm:ss' );
	    
	    $where = array(
		'fn_conta_id = ?' => $this->_data['fn_conta_id'],
		'fn_conta_status = ?' => 1
	    );

	    $row = $dbConta->fetchRow( $where );

	    if ( !empty( $row ) ) {

		$where = array('fn_conta_id = ?' => $this->_data['fn_conta_id']);

		switch ( $this->_data['fn_lancamento_tipo'] ) {

		    //Credito
		    case 'C':

			$valor = $row->fn_conta_saldo + $this->_data['fn_lancamento_valor'];
			$data = array('fn_conta_saldo' => $valor);

			$dbConta->update( $data, $where );

			break;

		    //Debito
		    case 'D':

			$valor = $row->fn_conta_saldo - $this->_data['fn_lancamento_valor'];
			$data = array('fn_conta_saldo' => $valor);

			$dbConta->update( $data, $where );

			break;
		}
	    }
	}
    }

    /* Validacoes */

    /**
     * Verifica se esta sendo envido lancamentos por projeto 
     *
     * @access protected
     * @return bool
     */
    protected function _validLancamentoProjeto()
    {
	if ( empty( $this->_data['projeto_id'] ) ) {

	    $this->_message->addMessage(
		    'É necessario pelo menos um lançamento por projeto', App_Message::WARNING
	    );

	    return false;
	}

	return true;
    }

    /**
     * Verifica se o valor total de cheques selecionados e inferior ao valor do docuemnto fiscal
     * 
     * @access protected
     * @return bool
     */
    protected function _validLancamentoCheque()
    {
	if ( !empty( $this->_data['fn_cheque_id'] ) ) {

	    $dbCheque = App_Model_DbTable_Factory::get( 'Cheque' );
	    $dbDocFiscalItens = App_Model_DbTable_Factory::get( 'DocumentoFiscalItens' );

	    $subSelect = $dbCheque->select()
		    ->from(
			    $dbCheque, array(new Zend_Db_Expr( 'SUM(fn_cheque_valor)' ))
		    )
		    ->where( 'fn_cheque_id IN(?)', $this->_data['fn_cheque_id'] );

	    $select = $dbDocFiscalItens->select()
		    ->from(
			    array('dfi' => $dbDocFiscalItens), array(
			'doc_fiscal' => new Zend_Db_Expr( 'SUM(fn_doc_fiscal_item_valor * fn_doc_fiscal_item_qtde)' ),
			'cheque' => new Zend_Db_Expr( '(' . $subSelect . ')' )
			    )
		    )
		    ->where( 'dfi.fn_doc_fiscal_id = ?', $this->_data['fn_doc_fiscal_id'] );

	    $row = $dbDocFiscalItens->fetchRow( $select );

	    if ( $row->cheque > $row->doc_fiscal ) {

		$this->_message->addMessage(
			'O valor total dos cheques não pode ser superior ao valor do documento fiscal', App_Message::WARNING
		);

		return false;
	    }
	}

	return true;
    }

    /**
     * Verifica se os dados do lancamento estao de acordo com o documento fiscal selecionado
     * 
     * @access protected
     * @return bool
     */
    public function validaDocumentoFiscal()
    {
	$dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
	$dbDocFiscalItens = App_Model_DbTable_Factory::get( 'DocumentoFiscalItens' );

	$subSelect = $dbDocumentoFiscal->select()
		->from(
			array('df' => $dbDocumentoFiscal), array('terceiro_id_remetente')
		)
		->where( 'df.fn_doc_fiscal_id = dfi.fn_doc_fiscal_id' );

	$select = $dbDocFiscalItens->select()
		->from(
			array('dfi' => $dbDocFiscalItens), array(
		    'valor' => new Zend_Db_Expr( 'SUM(fn_doc_fiscal_item_valor * fn_doc_fiscal_item_qtde)' ),
		    'terceiro_id' => new Zend_Db_Expr( '(' . $subSelect . ')' )
			)
		)
		->where( 'dfi.fn_doc_fiscal_id = ?', $this->_data['fn_doc_fiscal_id'] );

	$row = $dbDocFiscalItens->fetchRow( $select );

	//Verifica Fornecedor
	if ( $row->terceiro_id != $this->_data['terceiro_id'] ) {

	    $this->_message->addMessage(
		    'Deve ser selecionado o fornecedor remetente do documento fiscal', App_Message::WARNING
	    );

	    return false;
	}
    
	$fn_lanc_projeto_valor = number_format( array_sum( $this->_data['fn_lanc_projeto_valor'] ), 2, ',', '' );
	$valor_doc_fiscal      = number_format( $row->valor, 2, ',', '' );

	//Verifica valores
	if ( $valor_doc_fiscal != $fn_lanc_projeto_valor ) {

	    $this->_message->addMessage(
		    'Valor não corresponde com documento fiscal, R$' . $valor_doc_fiscal, App_Message::WARNING
	    );

	    return false;
	}

	return true;
    }

    /**
     * Verifica se a conta pode ser usada
     * 
     * @access protected
     * @return bool
     */
    protected function _validConta()
    {
	if ( empty( $this->_data['fn_conta_id'] ) ) {
	    $this->_message->addMessage( 'Selecione uma conta', App_Message::WARNING );
	    return false;
	}

	$dbConta = App_Model_DbTable_Factory::get( 'Conta' );

	$where = array(
	    'fn_conta_id = ?' => $this->_data['fn_conta_id'],
	    'fn_conta_status = ?' => 1
	);

	$row = $dbConta->fetchRow( $where );

	//Disponibilidade da conta
	if ( empty( $row ) ) {
	    $this->_message->addMessage( 'Conta desativada', App_Message::WARNING );
	    return false;
	}

	return true;
    }

}
