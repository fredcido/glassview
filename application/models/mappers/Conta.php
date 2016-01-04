<?php

/**
 * 
 * @version $Id: Conta.php 900 2013-06-04 20:44:01Z helion $
 */
class Model_Mapper_Conta extends App_Model_Mapper_Abstract
{
    
    public function fetchGrid()
    {

	$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
	$dbBanco = App_Model_DbTable_Factory::get( 'Banco' );

	$select = $dbConta->select()
		->setIntegrityCheck( false )
		->from(
			array('c' => $dbConta), array('c.*')
		)
		->joinLeft(
		array('b' => $dbBanco), 'b.fn_banco_id  = c.fn_banco_id ',
                array('b.fn_banco_nome','b.fn_banco_codigo')
	);

	$rows = $dbConta->fetchAll( $select );

        $currency = new Zend_Currency();

        $data = array( 'rows' => array() );

	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_conta_id,
		    'data' => array(
			++$key,
			$row->fn_conta_descricao,
			$row->fn_banco_nome,
			$row->fn_conta_agencia,
			$row->fn_conta_numero,
			$currency->setValue( (float)$row->fn_conta_saldo)->toString(),
			parent::_showStatus($row->fn_conta_status)
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Conta' );
	    
	    $where = array( 'UPPER(fn_conta_agencia) = UPPER(?)' => $this->_data['fn_conta_agencia'],
                            'UPPER(fn_conta_numero) = UPPER(?)'  => $this->_data['fn_conta_numero']);
		
	    if ( !$dbTable->isUnique( $where, $this->_data['fn_conta_id'] ) ) {

                $translate = Zend_Registry::get('Zend_Translate');
                
		$this->_message->addMessage( $translate->_('Conta já cadastrado.'), App_Message::ERROR );
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
        $where = array( 'fn_conta_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Conta' ), $where );
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function varificaLancamentosConta( $contaid )
    {

        $dbLancamento  = App_Model_DbTable_Factory::get( 'Lancamento' );

	$select = $dbLancamento->select()
		->from(
			array('l' => $dbLancamento), array('l.*')
		)
                ->where( 'l.fn_conta_id = ?' , $contaid );

	$rows = $dbLancamento->fetchAll( $select );

        if ( $rows->count() != 0 ) {

             return true;
        }else{

            return false;
        }

    }
    
	/**
	 * Busca contas destino para realizar transferencia 
	 * 
	 * @access public
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchContaDestino()
	{
		$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
		
		$select = $dbConta->select()
			->from(
				array( 'c' => $dbConta ),
				array( 'fn_conta_id', 'fn_conta_descricao' )
			)
			->where( 'c.fn_conta_status = ?', 1 )
			->where( 'c.fn_conta_id <> ?', $this->_data['fn_conta_id'] );
			
		$rows = $dbConta->fetchAll( $select );
		
		return $rows;
	}
	
	/**
	 * Busca saldo de acordo com a conta se a mesma estiver ativada
	 * 
	 * @access public
	 * @return Zend_Db_Table_Row
	 */
	public function fetchSaldo()
	{
		$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
                
		$where = array(
			'fn_conta_status = ?' => 1,
			'fn_conta_id = ?' => $this->_data['fn_conta_id']
		);
		
		$row = $dbConta->fetchRow( $where );
		
		return $row;
	}
	
	public function transferir()
	{	
	    try {

		    $adapter = Zend_Db_Table::getDefaultAdapter();

		    $adapter->beginTransaction();

		    if ( !empty( $this->_data['fn_lancamento_id_destino'] ) ) {
			
			$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
			
			$data = array(
			    'fn_lancamento_data' => $this->_data['fn_lancamento_data']
			);
			
			$where = $adapter->quoteInto( 'fn_lancamento_id = ?', $this->_data['fn_lancamento_id_destino'] );
			$dbLancamento->update( $data, $where );
			
			$where = $adapter->quoteInto( 'fn_lancamento_id = ?', $this->_data['fn_lancamento_id_origem'] );
			$dbLancamento->update( $data, $where );
			
		    } else {
		    
			parent::setValidators( array('_validTransferencia') );

			if ( !parent::isValid() )
			    return false;

			//Debito
			$this->_refreshSaldo( $this->_data['conta_origem'], 'D' );
			$id = $this->_saveLancamentoDebito();

			//Credito
			$this->_refreshSaldo( $this->_data['conta_destino'], 'C' );
			$this->_saveLancamentoCredito( $id );
		    }
		    
		    $this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );
		    $adapter->commit();

		    return true;

	    } catch ( Exception $e ) {

		    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );

		    $adapter->rollBack();

	    }
			
	}

	/**
	 * @access protected
	 * @param int $conta_id
	 * @param string $type [D]ebito, [C]redito
	 * @return void
	 */
	protected function _refreshSaldo( $conta_id, $type )
	{
		$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
		
		$where = array('fn_conta_id = ?' => $conta_id);
				
		$row = $dbConta->fetchRow( $where );
		
		switch ( $type ) {
			
			case 'C':
				$saldo = $row->fn_conta_saldo + $this->_data['valor'];
				break;
				
			case 'D':
				$saldo = $row->fn_conta_saldo - $this->_data['valor'];
				break;
				
			default:
				throw new Exception('Tipo de lançamento inválido');
				
		}
		
		$data = array('fn_conta_saldo' => $saldo);
		
		$dbConta->update($data, $where);
	}

	/**
	 * Transferencia Debito 
	 * 
	 * @access protected
	 * @return int
	 */
	protected function _saveLancamentoDebito()
	{
		$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
                
                if(empty($this->_data['fn_lancamento_dtefetivado'])){
                    
                    $this->_data['fn_lancamento_dtefetivado'] = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
                }
                
		$data = array(
			'fn_conta_id'                   => $this->_data['conta_origem'],
			'fn_lancamento_data' 		=> $this->_data['fn_lancamento_data'],
			'fn_lancamento_valor' 		=> $this->_data['valor'],
			'fn_lancamento_tipo' 		=> 'D',
			'fn_lancamento_dtefetivado'     => $this->_data['fn_lancamento_dtefetivado'],
			'fn_lancamento_efetivado' 	=> 1,
			'fn_lancamento_trans' 		=> 1,
			'fn_lancamento_obs' 		=> $this->_data['fn_lancamento_obs'],
			'fn_lancamento_status' 		=> 'A'
		);

		return $dbLancamento->insert( $data );
	}
	
	/**
	 * Transferencia Credito
	 * 
	 * @access 	protected
	 * @param 	int $id 
	 * 			ID do lancamento de debito da transferencia
	 * @return int
	 */
	protected function _saveLancamentoCredito( $id )
	{
		$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
		
                if(empty($this->_data['fn_lancamento_dtefetivado'])){
                    
                    $this->_data['fn_lancamento_dtefetivado'] = Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss');
                }
                
		$data = array(
			'fn_lancamento_anterior' 	=> $id,
			'fn_conta_id' 			=> $this->_data['conta_destino'],
			'fn_lancamento_data' 		=> $this->_data['fn_lancamento_data'],
			'fn_lancamento_valor' 		=> $this->_data['valor'],
			'fn_lancamento_tipo' 		=> 'C',
			'fn_lancamento_dtefetivado'	=> $this->_data['fn_lancamento_dtefetivado'],
			'fn_lancamento_efetivado' 	=> 1,
			'fn_lancamento_trans' 		=> 1,
			'fn_lancamento_obs' 		=> $this->_data['fn_lancamento_obs'],
			'fn_lancamento_status' 		=> 'A'
		);

		return $dbLancamento->insert( $data );		
	}
	
	/**
	 * @access protected
	 * @return bool
	 */
	protected function _validTransferencia()
	{
		$where = array(
			'fn_conta_status = ?' 	=> 1,
			'fn_conta_id = ?' 		=> $this->_data['conta_origem']
		);
		
		$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
		
		$row = $dbConta->fetchRow( $where );
		
		if ( $row->fn_conta_saldo < $this->_data['valor'] ) {
			$this->_message->addMessage( 'Saldo insuficiente', App_Message::WARNING );
			return false;	
		}
		
		return true;
	}
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_row
	 */
	public function fetchTransferencia( $id )
	{
	    $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
	    $dbConta      = App_Model_DbTable_Factory::get( 'Conta' );

	    $select = $dbLancamento->select()
				   ->from( 
					array ( 'ld' => $dbLancamento ),
					array(
					    'fn_lancamento_id',
					    'fn_lancamento_id_destino' => 'fn_lancamento_id',
					    'fn_lancamento_data',
					    'fn_lancamento_dtefetivado',
					    'fn_lancamento_obs',
					    'valor' => 'fn_lancamento_valor',
					    'fn_conta_id',
					    'conta_destino'  => 'fn_conta_id'
					)
				    )
				   ->setIntegrityCheck( false )
				   ->join(
					array ( 'lc' => $dbLancamento ),
					'lc.fn_lancamento_id = ld.fn_lancamento_anterior',
					array( 
					    'conta_origem'		=> 'fn_conta_id',
					    'fn_lancamento_id_origem'	=> 'fn_lancamento_id',
					)
				   )
				   ->where( 'ld.fn_lancamento_id = ?', $id );
	    
	    return $dbLancamento->fetchRow( $select );
	}
}