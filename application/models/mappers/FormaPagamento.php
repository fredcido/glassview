<?php

/**
 * 
 * @version $Id $
 */
class Model_Mapper_FormaPagamento extends App_Model_Mapper_Abstract
{
	/**
	 * @access public
	 * @return bool
	 */
	public function save()
	{
            
            $this->_data = $this->_data["lancamento"];

            $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );

            $selectLancamento = $dbLancamento->select()
                                             ->from( array( 'l' => $dbLancamento ) )
                                             ->where( 'l.fn_lancamento_id = ?', $this->_data['fn_lancamento_id'] );

            $dadosLancamento = $dbLancamento->fetchRow( $selectLancamento )->toArray();


            if ( round($dadosLancamento["fn_lancamento_valor"],2) !=  (float)$this->_data["fn_forma_pgto_valor"] ) {

                $this->_message->addMessage( 'O valor efetivado não confere com o valor do lançamento!', App_Message::WARNING );
                return false;
                
            }

            $this->_data['dados_bd_lancamento'] = $dadosLancamento;
            if ( !$this->_validaTipoLancamento() ) {

                return false;
            }

            $this->_data["fn_forma_pgto_valor"] = $dadosLancamento["fn_lancamento_valor"];
            $this->_data["fn_lancamento_tipo"]  = $dadosLancamento["fn_lancamento_tipo"];

            $dbFormaPagamento = App_Model_DbTable_Factory::get( 'FormaPagamento' );

            $adapter = Zend_Db_Table::getDefaultAdapter();

            $adapter->beginTransaction();
            try {
                    if( $this->_data['fn_conta_id'] != $dadosLancamento['fn_conta_id'] ){

                        if(!$this->_alterEfetivacaoConta( $dadosLancamento, $this->_data )){

                            $adapter->rollBack();
                            return false;
                        }
                    }
                    $this->_efetivarLancamento();

                    if ( (int)$dadosLancamento["fn_lancamento_efetivado"] != 1 )
                        $this->_efetivarConta();

                    parent::_simpleSave( $dbFormaPagamento );

                    $this->_saveTipoLancamentoProjeto();

                    $adapter->commit();

                    return true;

            } catch ( Exception $e ) {

                    $adapter->rollBack();
                    
                    $this->_message->addMessage( $e->getMessage(), App_Message::WARNING );
                    
                    return false;

            }

	}
	
	/**
	 * @access protected
	 * @return void
	 */
	protected function _efetivarLancamento()
	{
		$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
		
		$where = array( 'fn_lancamento_id = ?' => (int)$this->_data['fn_lancamento_id'] );
		
		$data = array(
			'fn_lancamento_dtefetivado' => $this->_data['fn_lancamento_dtefetivado'],
			'fn_conta_id'               => (int)$this->_data['fn_conta_id'],
			'fn_lancamento_efetivado'   => 1
		);
		
		$dbLancamento->update( $data, $where );
	}

	/**
	 * 
	 * @access protected
	 * @return void
	 */	
	protected function _efetivarConta()
	{
            $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );

            $where = array( 'fn_lancamento_id = ?' => $this->_data['fn_lancamento_id'] );

            $row = $dbLancamento->fetchRow( $where );

            if ( !empty($row->fn_conta_id) ) {

                $dbConta = App_Model_DbTable_Factory::get( 'Conta' );

                $where = array( 'fn_conta_id = ?' => $row->fn_conta_id );

                $row = $dbConta->fetchRow( $where );

                if ( 'D' === $this->_data['fn_lancamento_tipo'] )
                        $saldo = $row->fn_conta_saldo - $this->_data['fn_forma_pgto_valor'];
                else
                        $saldo = $row->fn_conta_saldo + $this->_data['fn_forma_pgto_valor']; 

                $data = array( 'fn_conta_saldo' => $saldo );

                $dbConta->update( $data, $where );

            }
	}
        
	/**
	 * 
	 * @access protected
	 * @return bool
	 */
        private function _alterEfetivacaoConta( $dadosBd, $dadosTela )
        {
            $dbConta = App_Model_DbTable_Factory::get( 'Conta' );
	    $subContaTela = $dbConta->select()
		    ->from(
			    $dbConta, 'fn_conta_saldo'
		    )
		    ->where( 'fn_conta_id = ?', (int)$dadosTela['fn_conta_id'] );
            
	    $select = $dbConta->select()
		    ->from(
			    array('dfi' => $dbConta), 
                            array(
                                    'saldo_conta_tela' => new Zend_Db_Expr( '(' . $subContaTela . ')' ),
                                    'saldo_conta_bd'   => 'fn_conta_saldo'
			    )
		    )
		    ->where( 'fn_conta_id = ?', (int)$dadosBd['fn_conta_id'] );

	    $row = $dbConta->fetchRow( $select );
            
            if ( 'D' === $dadosBd['fn_lancamento_tipo'] ){
                
                $saldoContaTela = $row->saldo_conta_tela - $dadosBd['fn_lancamento_valor'];
                $saldoContaBD   = $row->saldo_conta_bd   + $dadosBd['fn_lancamento_valor'];
            }else{
                
                $saldoContaTela = $row->saldo_conta_tela + $dadosBd['fn_lancamento_valor'];
                $saldoContaBD   = $row->saldo_conta_bd   - $dadosBd['fn_lancamento_valor'];
            }

            try{
                
                $whereContaTela = $dbConta->getAdapter()->quoteInto( 'fn_conta_id = ?', (int)$dadosTela['fn_conta_id'] );
                $dbConta->update( array( 'fn_conta_saldo' => $saldoContaTela ), $whereContaTela );

                $whereContaBD   = $dbConta->getAdapter()->quoteInto( 'fn_conta_id = ?', (int)$dadosBd['fn_conta_id'] );
                $dbConta->update( array( 'fn_conta_saldo' => $saldoContaBD )  , $whereContaBD );

                return true;
                
            } catch ( Exception $e ) {

                    $this->_message->addMessage( $e->getMessage(), App_Message::WARNING );
                    
                    return false;
            }
        }
	
	/* Validators */
        
        private function _validaTipoLancamento() 
        {    
            if(!empty($this->_data['fn_lanc_projeto_valor'])){
                
                $dadosValidar = $this->_data;
                $dadosValidar['fn_doc_fiscal_id']      = $this->_data['dados_bd_lancamento']['fn_doc_fiscal_id'];
                $dadosValidar['terceiro_id']           = $this->_data['dados_bd_lancamento']['terceiro_id'];
                $mapperLancamento = new Model_Mapper_Lancamento();
                $mapperLancamento->setData($dadosValidar);
                
                if( !$mapperLancamento->validaDocumentoFiscal() ){

                    $this->_message = $mapperLancamento->getMessage();
                    return false;
                }
            }
            return true;
        }
        
        private function _saveTipoLancamentoProjeto() 
        {
            $dadosSalvar = array();
            $dadosSalvar['projeto_id'] = $this->_data['projeto_id'];
            $dadosSalvar['fn_tipo_lanc_id'] = $this->_data['fn_tipo_lanc_id'];
            $dadosSalvar['fn_lanc_projeto_valor'] = $this->_data['fn_lanc_projeto_valor'];
            $mapperLancamento = new Model_Mapper_Lancamento();
            $mapperLancamento->setData($dadosSalvar);
            $mapperLancamento->saveTipoLancamentoProjeto( (int)$this->_data["fn_lancamento_id"] );
        }
	
	
}
