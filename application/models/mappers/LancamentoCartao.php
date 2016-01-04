<?php

/**
 * 
 * @version $Id: LancamentoCartao.php 910 2013-06-11 12:43:00Z fred $
 */
class Model_Mapper_LancamentoCartao extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        $dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
        $dbCartaoCredito    = App_Model_DbTable_Factory::get( 'CartaoCredito' );
        
        
	$select = $dbLancamentoCartao->select()
		->setIntegrityCheck( false )
		->from(
			array('l' => $dbLancamentoCartao), array('l.*')
		)
		->join(
		array('c' => $dbCartaoCredito), 'c.fn_cc_id  = l.fn_cc_id ', 
                         array('c.fn_cc_descricao')
	);
        
        $rows = $dbLancamentoCartao->fetchAll( $select );
        
        $data = array('rows' => array());
        $date = new Zend_Date();
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $currency = new Zend_Currency();
                
                if( empty( $row->fn_lanc_cartao_data )){
                    
                    $LancCartaoData = '-';
                }else{
                    $date->set( $row->fn_lanc_cartao_data );
                    
                    $LancCartaoData = $date->toString( 'dd/MM/yyyy' );
                }
                
                $data['rows'][] = array(
                    'id'    => $row->fn_lanc_cartao_id,
                    'data'  => array(
                        ++$key,
                        $LancCartaoData,
                        $row->fn_cc_descricao,
                        $row->fn_lanc_cartao_desc,
                        $currency->setValue( $row->fn_lanc_cartao_valor )->toString(),
                        $this->_getStatusLancamentoCartaoCreditos($row->fn_lanc_cartao_status)
                    )
                );
                
            }
            
        }
        
        return $data;
    }
    
    public function save()
    {
	$adapter = Zend_Db_Table::getDefaultAdapter();

	try {
            
            parent::setValidators( array('_validDocumentoFiscal') );

            if ( parent::isValid() ) {
                
                $lancTotal = (float)0;
                foreach ($this->_data['fn_lanc_cc_tipo_valor'] as $lancValor) {

                    if(empty($lancValor)){

                        $this->_message->addMessage( 'Informar valor de lançamento.', App_Message::ERROR );
                        return false;
                    }
                    $lancTotal = $lancTotal + $lancValor;
                }

                $this->_data['fn_lanc_cartao_valor'] = $lancTotal;

                $dbLancamentoCartao     = App_Model_DbTable_Factory::get('LancamentoCartao');
                $dbLancamentoCartaoTipo = App_Model_DbTable_Factory::get('LancamentoCartaoTipo');

                $adapter->beginTransaction();

                $where = array( 'UPPER(fn_lanc_cartao_desc) = UPPER(?)'  => $this->_data['fn_lanc_cartao_desc'] ,
                                'fn_lanc_cartao_data = ?'                => $this->_data['fn_lanc_cartao_data'],
                                'fn_cc_id = ?'                           => $this->_data['fn_cc_id'] );

                if ( !$dbLancamentoCartao->isUnique( $where, $this->_data['fn_lanc_cartao_id'] ) ) {

                    $this->_message->addMessage( 'Lançamento já cadastrado.', App_Message::ERROR );
                    return false;
                }

                $lancsBD = array();
                $result  = parent::_simpleSave( $dbLancamentoCartao );
                if(empty($this->_data['fn_lanc_cartao_id'])){

                    $lancCartaoId = $result;
                }else{

                    $lancCartaoId  = $this->_data['fn_lanc_cartao_id'];
                    $lancBD        = $this->listTiposLancamentos( $lancCartaoId );

                    foreach ( $lancBD as $lanc ) {

                        $lancsBD[] = $lanc['fn_lanc_cartao_tipo_id'];
                    }
                }


                $lancsTela = array();
                $dataLancsTela = array();
                foreach( $this->_data["fn_tipo_lanc_id"] as $key => $value){

                    if( empty($this->_data["fn_lanc_cartao_tipo_id"][$key]) ){

                        $dataTipo = array(
                                        "fn_lanc_cartao_id"     => $lancCartaoId,
                                        "fn_tipo_lanc_id"       => $this->_data["fn_tipo_lanc_id"][$key],
                                        "fn_lanc_cc_tipo_valor" => $this->_data["fn_lanc_cc_tipo_valor"][$key]
                                    );

                        $dbLancamentoCartaoTipo->insert( $dataTipo );
                    }else{

                        $idxid = $this->_data['fn_lanc_cartao_tipo_id'][$key];
                        $lancsTela[] = $idxid;
                        $dataLancsTela[$idxid] = array(
                                    "fn_tipo_lanc_id"       => $this->_data["fn_tipo_lanc_id"][$key],
                                    "fn_lanc_cc_tipo_valor" => $this->_data["fn_lanc_cc_tipo_valor"][$key]
                        );
                    }
                }

                $deleteLanc = array_diff( $lancsBD, $lancsTela );
                $updateLanc = array_diff( $lancsBD, $deleteLanc );

                foreach ($updateLanc as $updateLancId) {

                    $where = array( 
                        'fn_lanc_cartao_id = ?'      => $lancCartaoId,
                        'fn_lanc_cartao_tipo_id = ?' => $updateLancId
                    );

                    $dbLancamentoCartaoTipo->update( $dataLancsTela[$updateLancId] , $where );
                }

                foreach ($deleteLanc as $deleteId) {

                    $where = array(
                        'fn_lanc_cartao_id = ?'      => $lancCartaoId,
                        'fn_lanc_cartao_tipo_id = ?' => $deleteId
                    );

                    $dbLancamentoCartaoTipo->delete( $where );
                }

                $adapter->commit();
                return $result;
            }
	    
	} catch ( Exception $e ) {

	    $adapter->rollBack();
	    
	    return false;
	}
    }

    /**
    * Verifica se os dados do lancamento estao de acordo com o documento fiscal selecionado
    * 
    * @access protected
    * @return bool
    */
    protected function _validDocumentoFiscal()
    {
        
        if(empty($this->_data["fn_tipo_lanc_id"])){

            $this->_message->addMessage( 'Nenhum lançamento adicionado.', App_Message::ERROR );
            return false;
        }
        
        $dbDocumentoFiscal 	= App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        $dbDocFiscalItens 	= App_Model_DbTable_Factory::get( 'DocumentoFiscalItens' );

        $subSelect = $dbDocumentoFiscal->select()
                ->from(
                        array( 'df' => $dbDocumentoFiscal ),
                        array( 'terceiro_id_remetente' )
                )
                ->where( 'df.fn_doc_fiscal_id = dfi.fn_doc_fiscal_id' );

        $select = $dbDocFiscalItens->select()
                ->from(
                        array( 'dfi' => $dbDocFiscalItens ),
                        array(
                                'valor'         => new Zend_Db_Expr('SUM(fn_doc_fiscal_item_valor * fn_doc_fiscal_item_qtde)'),
                                'terceiro_id' 	=> new Zend_Db_Expr('(' . $subSelect . ')')
                        )
                )
                ->where( 'dfi.fn_doc_fiscal_id = ?', $this->_data['fn_doc_fiscal_id'] );

        
        $row = $dbDocFiscalItens->fetchRow( $select );

        $fn_lanc_cc_tipo_valor = array_sum( $this->_data['fn_lanc_cc_tipo_valor'] );

        $dataBd   = number_format( $row->valor, 2, ',', '' );
        $dataTela = number_format( $fn_lanc_cc_tipo_valor, 2, ',', '' );

        //Verifica valores
        if ( $dataBd != $dataTela ) {

                $this->_message->addMessage( 
                        'Valor não corresponde com documento fiscal, R$' . $dataBd, 
                        App_Message::WARNING 
                );

                return false;

        }

        return true;
    }

    public function listTiposLancamentos( $id )
    {
	$dbLancamentoCartaoTipo = App_Model_DbTable_Factory::get('LancamentoCartaoTipo');
        $dbTipoLancamento       = App_Model_DbTable_Factory::get('TipoLancamento');
        
        
	$select = $dbLancamentoCartaoTipo->select()
		->setIntegrityCheck( false )
		->from(
			array('lc' => $dbLancamentoCartaoTipo), array('lc.*')
		)
		->join(
		array('tl' => $dbTipoLancamento), 'tl.fn_tipo_lanc_id  = lc.fn_tipo_lanc_id ', 
                         array('tl.projeto_id')
                )
                ->where( 'lc.fn_lanc_cartao_id = ?' , $id );


	return $dbLancamentoCartaoTipo->fetchAll( $select );
    }
    
    /**
     *
     * @param type $id
     * @return type 
     */
    public function verificaLancamentoFatura( $id )
    {
	$dbLancamentoFatura = App_Model_DbTable_Factory::get('LancamentoFatura');

	$select = $dbLancamentoFatura->select()
		->from(
			array('lf' => $dbLancamentoFatura), array('lf.fn_cc_fat_id')
		)
                ->where( 'lf.fn_lanc_cartao_id = ?' , $id );

	$rows = $dbLancamentoFatura->fetchAll( $select );
       
        if ( $rows->count() == 0 ) {
            
            return false;
        }else{
            
            return true;
        }
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'fn_lanc_cartao_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'LancamentoCartao' ), $where );
    }
    
    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getStatusLancamentoCartaoCreditos( $type )
    {
	$optStatus = array(  
                                'P' => 'Pendente' , 
                                'F' => 'Faturado'
                          );

	return ( empty( $optStatus[$type] ) ? '-' : $optStatus[$type] );
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function buscaLancamentosCartao( $data )
    {
	// Busca lancamentos pendentes do cartao de credito
	$lancamentosPendentes = $this->listLancamentosNotInFatura( $data['cartao'] );
	
	// Verifica se tem alguma fatura do cartao que nao esteja efetivada
	$fatura = $this->buscaFaturaCartaoCredito( $data['cartao'], 0 );
	
	$retorno = array(
	    'lancamentos' => $this->_lancamentosToJsonGrid( $lancamentosPendentes ),
	    'fatura'	  => false
	);
	
	if ( !empty( $fatura ) ) {
	    
	    $retorno['fatura'] = $fatura->toArray();
	    $retorno['efetivados'] = $this->_lancamentosToJsonGrid( $this->listLancamentosFatura( $fatura->fn_cc_fat_id ) );
	    
	    $retorno['fatura']['fn_cc_fat_total'] = (float)$retorno['fatura']['fn_cc_fat_total'];
	}
	
	return $retorno;
    }
    
    /**
     *
     * @param int $cc_id
     * @return Zend_Db_Table_Rowset 
     */
    public function listLancamentosNotInFatura( $cc_id )
    {
	$dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
	$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
	
	$subSelect = $dbLancamentoFatura->select()
					->from( 
					    array( 'lf' => $dbLancamentoFatura ),
					    array( new Zend_Db_Expr( 'NULL' ) )
					)
					->where( 'lf.fn_lanc_cartao_id = lc.fn_lanc_cartao_id' );
	
	$select = $dbLancamentoCartao->select()
				     ->from( array( 'lc' => $dbLancamentoCartao ) )
				     ->setIntegrityCheck( false )
				     ->where( 'lc.fn_cc_id = ?', $cc_id )
				     ->where( 'lc.fn_lanc_cartao_status = ?', 'P' )
				     ->where( 'NOT EXISTS (?)', new Zend_Db_Expr( $subSelect ) )
				     ->order( 'lc.fn_lanc_cartao_data' );
	
	return $dbLancamentoCartao->fetchAll( $select );
    }
    
    /**
     *
     * @param int $fatura_id
     * @return Zend_Db_Table_Rowset 
     */
    public function listLancamentosFatura( $fatura_id )
    {
	$dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
	$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
	
	$select = $dbLancamentoCartao->select()
				     ->from( array( 'lc' => $dbLancamentoCartao ) )
				     ->setIntegrityCheck( false )
				     ->join(
					 array( 'lf' => $dbLancamentoFatura ),
					 'lc.fn_lanc_cartao_id = lf.fn_lanc_cartao_id',
					 array()
				     )
				     ->where( 'lf.fn_cc_fat_id = ?', $fatura_id )
				     ->order( 'lc.fn_lanc_cartao_data' );
	
	return $dbLancamentoCartao->fetchAll( $select );
    }
    
    /**
     *
     * @param $data
     * @return Zend_Db_Table_Rowset 
     */
    protected function _lancamentosToJsonGrid( $data )
    {
	$json['rows'] = array();
	
	$currency = new Zend_Currency();
	$date = new Zend_Date();
	
	foreach ( $data as $row ) {
	    
	    $json['rows'][] = array(
		'id'    => $row->fn_lanc_cartao_id,
		'data'  => array(
		    $row->fn_lanc_cartao_desc,
		    $currency->setValue( $row->fn_lanc_cartao_valor )->toString(),
		    $date->set( $row->fn_lanc_cartao_data )->toString('dd/MM/Y')
		)
	    );
	}
	
	return $json;
    }
    
    /**
     *
     * @param int $cc_id
     * @param int $efetivado
     * @return Zend_Db_Table_Row 
     */
    public function buscaFaturaCartaoCredito( $cc_id, $efetivado = null )
    {
	$dbFatura = App_Model_DbTable_Factory::get( 'Fatura' );
	
	$select = $dbFatura->select()
			   ->from( array( 'fc' => $dbFatura ) )
			   ->setIntegrityCheck( false )
			   ->where( 'fc.fn_cc_id = ?', $cc_id );
	
	if ( !is_null( $efetivado ) )
	    $select->where( 'fc.fn_cc_fat_efetivado = ?', $efetivado );
	
	return $dbFatura->fetchRow( $select );
	    
    }
    
    /**
     *
     * @param int $cc_id
     * @return Zend_Db_Table_Rowset 
     */
    public function listLancamentosPendentes( $cc_id )
    {
	return $this->listLancamentos( $cc_id, 'P' );
    }
    
    /**
     *
     * @param int $cc_id
     * @return Zend_Db_Table_Rowset 
     */
    public function listLancamentosFaturados( $cc_id )
    {
	return $this->listLancamentos( $cc_id, 'F' );
    }
    
    /**
     *
     * @param int $cc_id
     * @param string $status
     * @return Zend_Db_Table_Rowset 
     */
    public function listLancamentos( $cc_id, $status = false )
    {
	$dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
	
	$select = $dbLancamentoCartao->select()
				     ->setIntegrityCheck( false )
				     ->from( 
					array( 'lc' => $dbLancamentoCartao ),
					array(
					    'fn_lanc_cartao_id',
					    'fn_lanc_cartao_desc',
					    'fn_lanc_cartao_valor',
					    'fn_lanc_cartao_data'
					)
				     )
				     ->where( 'fn_cc_id = ?', $cc_id )
				     ->order( 'fn_lanc_cartao_data' );
	
	if ( !empty( $status ) )
	    $select->where( 'fn_lanc_cartao_status = ?', $status );
	
	return $dbLancamentoCartao->fetchAll( $select );
    }
    
    /**
     *
     * @return array
     */
    public function salvarFatura()
    {
	$dbFatura = App_Model_DbTable_Factory::get( 'Fatura' );
	
	$dbFatura->getAdapter()->beginTransaction();
	try {
	    
	    $dataForm = $this->_data;
	      
	    $fatura_id = parent::_simpleSave( $dbFatura, false );
	    
	    $this->_saveLancamentosFatura( $dataForm, $fatura_id );
	    
	    $dbFatura->getAdapter()->commit();
	    
	    return array(
		'status'    => true,
		'fatura'    => $fatura_id
	    );
	    
	} catch ( Exception $e ) {
	    
	    $dbFatura->getAdapter()->rollBack();
	    
	    return array( 'status' => false );
	}
    }
    
    /**
     *
     * @param array $data
     * @param int $fatura_id 
     */
    protected function _saveLancamentosFatura( $data, $fatura_id )
    {
	$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
	    
	$where = $dbLancamentoFatura->getAdapter()->quoteInto( 'fn_cc_fat_id = ?', $fatura_id );
	$dbLancamentoFatura->delete( $where );

	foreach ( $data['lancamentos'] as $lancamento ) {

	    $row = $dbLancamentoFatura->createRow( 
			array( 
			    'fn_cc_fat_id'	    => $fatura_id,
			    'fn_lanc_cartao_id' => $lancamento
			) 
		    );

	    $row->save();
	}
    }
    
    /**
     *
     * @return array 
     */
    public function efetivarFatura()
    {
	$dbFatura = App_Model_DbTable_Factory::get( 'Fatura' );
	
	$dbFatura->getAdapter()->beginTransaction();
	try {
	  
	    $dataForm = $this->_data;
	    
	    $dataLancamento = array_merge( $dataForm['lancamento'], $dataForm );
	    $dataLancamento['fn_lancamento_efetivado'] = 1;
	    
	    $mapperLancamento = new Model_Mapper_Lancamento();
	    $lancamento_id = $mapperLancamento->setData( $dataLancamento )->save();
	    
	    if ( !$lancamento_id ) {
		
		$dbFatura->getAdapter()->rollBack();
		return array( 'status' => false, 'msg' => $mapperLancamento->getMessage()->getMessage( 0 ) );
	    } 
	    
	    // Efetiva a fatura
	    $this->_data['fn_cc_fat_efetivado'] = 1;
	    $this->_data['fn_lancamento_id'] = $lancamento_id;
	    
	    $fatura_id = parent::_simpleSave( $dbFatura, false );
	    
	    // Fatura todos os lancamentos do cartao
	    $dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
	    $whereUpdate = $dbLancamentoCartao->getAdapter()->quoteInto( 'fn_lanc_cartao_id IN (?)', (array)$dataForm['lancamentos'] );
	    $dbLancamentoCartao->update( array( 'fn_lanc_cartao_status' => 'F' ), $whereUpdate );
	    
	    $dbFatura->getAdapter()->commit();
	    
	    return array(
		'status'    => true,
		'fatura'    => $fatura_id,
		'permissao' => true
	    );
	    
	} catch ( Exception $e ) {
	    
	    $dbFatura->getAdapter()->rollBack();
	    
	    return array( 'status' => false );
	}
    }
    
    /**
     *
     * @return array
     */
    public function verificaPermissaoEfetivar()
    {
	if ( !$this->_session->acl->isAllowedToolbar( App_Plugins_Acl::getIdentifier( '/financeiro/lancamento/', 'Salvar' ) ) )
	    return array( 'permissao' => false );
	else
	    return array( 'permissao' => true );
    }
    
    /**
     *
     * @return array
     */
    public function preparaFaturaLancamento()
    {
	try {
	    
	    $dataForm = $this->_data;
	    
	    // Salva a fatura com os itens
	    $retorno = $this->salvarFatura();
	    if ( !$retorno['status'] )
		throw new Exception( 'Erro ao salvar fatura' );
	    
	    return array(
		'status' => true,
		'fatura' => $retorno['fatura'],
		'itens'	 => $this->agrupaTiposLancamento( $retorno['fatura'] )->toArray()
	    );
	    
	} catch ( Exception $e ) {
	    
	    return array( 'status' => false );
	}
    }
    
    /**
     *
     * @param int $fatura_id
     * @return Zend_Db_Table_Rowset
     */
    public function agrupaTiposLancamento( $fatura_id )
    {
	$dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
	$dbLancamentoCartaoTipo = App_Model_DbTable_Factory::get( 'LancamentoCartaoTipo' );
	
	$select = $dbProjeto->select()
			    ->setIntegrityCheck( false )
			    ->from(
				array( 'p' => $dbProjeto ),
				array( 
				    'projeto_id', 
				    'projeto_nome',
				    'total' => new Zend_Db_Expr( 'SUM( fn_lanc_cc_tipo_valor )' )
				)
			    )
			    ->join(
				array( 'tl' => $dbTipoLancamento ),
				'tl.projeto_id = p.projeto_id',
				array( 'fn_tipo_lanc_id', 'fn_tipo_lanc_desc' )
			    )
			    ->join(
				array( 'lct' => $dbLancamentoCartaoTipo ),
				'lct.fn_tipo_lanc_id = tl.fn_tipo_lanc_id',
				array()
			    )
			    ->join(
				array( 'lf' => $dbLancamentoFatura ),
				'lf.fn_lanc_cartao_id = lct.fn_lanc_cartao_id',
				array()
			    )
			    ->where( 'lf.fn_cc_fat_id = ?', $fatura_id )
			    ->group( array( 'fn_tipo_lanc_id' ) )
			    ->order( array( 'projeto_id' ) );
	
	return $dbProjeto->fetchAll( $select );
    }
}