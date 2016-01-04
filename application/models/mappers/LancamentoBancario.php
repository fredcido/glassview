<?php

/**
 *
 * @version $Id $
 */
class Model_Mapper_LancamentoBancario extends App_Model_Mapper_Abstract
{

    /**
     * 
     * @access public
     * @param string $tipo
     * @return array
     */
    public function fetchGrid( $tipo )
    {
	$dbLancamento       = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbConta            = App_Model_DbTable_Factory::get( 'Conta' );
	$dbDocumentoFiscal  = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
	$dbTerceiro         = App_Model_DbTable_Factory::get( 'Terceiro' );
        $dbDuplicata        = App_Model_DbTable_Factory::get( 'Duplicata' );

        $subSelect = $dbDuplicata->select()
            ->from(
                array( 'd' => $dbDuplicata ),
                array( 'fn_lancamento_id' )
            );

	$select = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('l' => $dbLancamento), array(
		    'fn_lancamento_id',
		    'fn_lancamento_data' => new Zend_Db_Expr( "DATE_FORMAT(l.fn_lancamento_data, '%d/%m/%Y')" ),
		    'fn_lancamento_valor'
			)
		)
		->joinLeft(
			array('c' => $dbConta), 'c.fn_conta_id = l.fn_conta_id', array('fn_conta_descricao')
		)
		->join(
			array('df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id', array('fn_doc_fiscal_numero')
		)
		->joinLeft(
			array('t' => $dbTerceiro), 't.terceiro_id = l.terceiro_id', array('terceiro_nome')
		)
		->where( 'l.fn_lancamento_status <> ?', 'I' )
		->where( 'l.fn_lancamento_efetivado = ?', 0 )
        ->where( 'l.fn_lancamento_tipo = ?', $tipo )
        ->where( 'l.fn_lancamento_id NOT IN(?)', $subSelect );
	
	$rows = $dbLancamento->fetchAll( $select );

	$data = array('rows' => array());
	$date = new Zend_Date();

	if ( $rows->count() ) {

	    $currency = new Zend_Currency();

	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_lancamento_id,
		    'data' => array(
			++$key,
			$row->fn_lancamento_data,
			$row->fn_conta_descricao,
			$row->fn_doc_fiscal_numero,
			$row->terceiro_nome,
			$currency->setValue( $row->fn_lancamento_valor )->toString(),
		    )
		);
	    }
	}
	
	return $data;
    }
    
    /**
     *
     * @return array
     */
    public function fetchEfetivados()
    {
	$dbLancamento       = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbConta            = App_Model_DbTable_Factory::get( 'Conta' );
	$dbDocumentoFiscal  = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
	$dbTerceiro         = App_Model_DbTable_Factory::get( 'Terceiro' );
	$dbDuplicata        = App_Model_DbTable_Factory::get( 'Duplicata' );

	$subSelect = $dbDuplicata->select()
	    ->from(
		array( 'd' => $dbDuplicata ),
		array( 'fn_lancamento_id' )
	    );

	$select = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
		    array('l' => $dbLancamento), 
		    array(
			'fn_lancamento_id',
			'fn_lancamento_data'	    => new Zend_Db_Expr( "DATE_FORMAT(l.fn_lancamento_data, '%d/%m/%Y')" ),
			'fn_lancamento_dtefetivado' => new Zend_Db_Expr( "DATE_FORMAT(l.fn_lancamento_dtefetivado, '%d/%m/%Y')" ),
			'fn_lancamento_valor',
			'fn_lancamento_tipo'
		    )
		)
		->joinLeft(
		    array( 'c' => $dbConta ), 
		    'c.fn_conta_id = l.fn_conta_id', 
		    array( 'fn_conta_descricao' )
		)
		->join(
		    array( 'df' => $dbDocumentoFiscal ), 
		    'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id', 
		    array( 'fn_doc_fiscal_numero' )
		)
		->joinLeft(
		    array( 't' => $dbTerceiro ), 
		    't.terceiro_id = l.terceiro_id', 
		    array( 'terceiro_nome' )
		)
		->where( 'l.fn_lancamento_status <> ?', 'I' )
		->where( 'l.fn_lancamento_efetivado = ?', 1 )
		->where( 'l.fn_lancamento_id NOT IN(?)', $subSelect );
	
	$rows = $dbLancamento->fetchAll( $select );

	$data = array( 'rows' => array() );
	$translate = Zend_Registry::get('Zend_Translate');

	if ( $rows->count() ) {

	    $currency = new Zend_Currency();

	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_lancamento_id,
		    'data' => array(
			++$key,
			$row->fn_lancamento_data,
			$row->fn_lancamento_dtefetivado,
			$translate->_( $row->fn_lancamento_tipo == 'C' ? 'Crédito' : 'Débito' ),
			$row->fn_conta_descricao,
			$row->fn_doc_fiscal_numero,
			$row->terceiro_nome,
			$currency->setValue( $row->fn_lancamento_valor )->toString(),
		    )
		);
	    }
	}
	
	return $data;
    }

    /**
     * 
     * @access public
     * @return mixed
     */
    public function save()
    {
	try {

	    $adapter = Zend_Db_Table::getDefaultAdapter();

	    $adapter->beginTransaction();

	    $mapperLancamento = new Model_Mapper_Lancamento();
	    $fn_lancamento_id = $mapperLancamento->setData( $this->_data )->save();
	    
	    if ( !$fn_lancamento_id ) {
		
		$this->_message->addMessage( $mapperLancamento->getMessage()->getMessage( 0 ), App_Message::ERROR );
		$adapter->rollBack();
		
		return false;
		
	    } else {
		
		$adapter->commit();
		$this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );
		
		return $fn_lancamento_id;
	    }
		
	} catch ( Exception $e ) {

	    $adapter->rollBack();

	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	}

	return false;
    }


    /**
     * 
     * @access public
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
	$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );

	$select = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('l' => $dbLancamento)
		)
		->join(
			array('df' => $dbDocumentoFiscal), 'l.fn_doc_fiscal_id = df.fn_doc_fiscal_id', array(
		    'df.fn_doc_fiscal_numero',
		    'df.fn_doc_fiscal_chave'
			)
		)
		->where( 'l.fn_lancamento_id = ?', $this->_data['id'] );

	$row = $dbLancamento->fetchRow( $select );

	return $row;
    }

    /**
     * 
     * @access public
     * @return Zend_Db_Table_Rowset 
     */
    public function fetchLancamentoProjeto()
    {
	$dbLancamentoProjeto = App_Model_DbTable_Factory::get( 'LancamentoProjeto' );

	$select = $dbLancamentoProjeto->select()
		->from(
			array('lp' => $dbLancamentoProjeto), array(
		    'projeto_id',
		    'fn_tipo_lanc_id',
		    'fn_lanc_projeto_valor'
			)
		)
		->where( 'lp.fn_lancamento_id = ?', $this->_data['fn_lancamento_id'] );

	$rows = $dbLancamentoProjeto->fetchAll( $select );

	return $rows;
    }  
    
    /**
     * 
     * @access public
     * @return Zend_Db_Table_Rowset 
     */
    public function isDeleteLancamento()
    {
	$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbDuplicata  = App_Model_DbTable_Factory::get( 'Duplicata' );
	$dbParcela    = App_Model_DbTable_Factory::get( 'Parcela' );

	$selectDuplicata = $dbDuplicata->select()
	    ->from(
		array( 'd' => $dbDuplicata ),
		array( 'count(d.fn_lancamento_id)' )
	    )->where(  'd.fn_lancamento_id = l.fn_lancamento_id' );
        
	$selectParcela = $dbParcela->select()
	    ->from(
		array( 'p' => $dbParcela ),
		array( 'count(p.fn_lancamento_id)' )
	    )->where(  'p.fn_lancamento_id = l.fn_lancamento_id' );

        
	$select = $dbLancamento->select()
		->from(
			array('l' => $dbLancamento), 
                        array(
                            'fn_lancamento_id',
                            'duplicata' => new Zend_Db_Expr( '('.$selectDuplicata.')' ),
                            'parcela'   => new Zend_Db_Expr( '('.$selectParcela.')' ),
                            'efetivado' => 'fn_lancamento_efetivado'
			)
		)
		->where( 'l.fn_lancamento_id = ?', $this->_data['fn_lancamento_id'] );

    
	$rows = $dbLancamento->fetchRow( $select )->toArray();

        if( empty($rows['duplicata']) && 
            empty($rows['parcela']) && 
            empty($rows['efetivado']) &&
            $this->_session->acl->isAllowedToolbar( App_Plugins_Acl::getIdentifier( '/financeiro/lancamento/', 'Deletar' ) )){
            
            return true;
        }else{
            
            return false;
        }
    }  
    
    /**
    * @access public
    * @return bool
    */
    public function delete()
    {
        if(!$this->isDeleteLancamento()){
            
            $this->_message->addMessage( 'Lançamento não pode ser removido!', App_Message::ERROR );

            return false;
        }
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $adapter->beginTransaction();
        
        try {

            $dbLancamentoProjeto = App_Model_DbTable_Factory::get( 'LancamentoProjeto');
            $dbLancamento        = App_Model_DbTable_Factory::get( 'Lancamento' );

            $where = array( 'fn_lancamento_id = ?' => $this->_data['fn_lancamento_id'] );

            $dbLancamentoProjeto->delete( $where );
            $dbLancamento->delete( $where );

            $this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );

            $adapter->commit();
            return true;
        } catch ( Exception $e ) {

            //var_dump( $e);
            $adapter->rollBack();
            $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );

            return false;

        } 

    }
}