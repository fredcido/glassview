<?php

/**
 * 
 * @version $Id: DocumentoFiscal.php 944 2013-08-20 17:12:41Z helion $
 */
class Model_Mapper_DocumentoFiscal extends App_Model_Mapper_Abstract
{
    
    public function fetchGrid()
    {

	$dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        $dbTerceiro        = App_Model_DbTable_Factory::get( 'Terceiro' );
        $dbDocumentoFiscalItens = App_Model_DbTable_Factory::get( 'DocumentoFiscalItens' );

	$select = $dbDocumentoFiscal->select()
		->setIntegrityCheck( false )
		->from(
			array( 'df' => $dbDocumentoFiscal),
                        array( 'df.*',
                               'total_doc_fiscal'  => '(' . new Zend_Db_Expr(
                                    $dbDocumentoFiscalItens->select()
                                        ->setIntegrityCheck( false )
                                        ->from(
                                            $dbDocumentoFiscalItens,
                                            array( 'SUM(fn_doc_fiscal_itens.fn_doc_fiscal_item_valor * fn_doc_fiscal_itens.fn_doc_fiscal_item_qtde)' )
                                        )
                                        ->where( 'fn_doc_fiscal_itens.fn_doc_fiscal_id = df.fn_doc_fiscal_id' )
                                    ) . ')'
                            )
		)
		->join(
                    array('tr' => $dbTerceiro), 'tr.terceiro_id = df.terceiro_id_remetente ',
                    array('terceiro_nome_remetente' => 'tr.terceiro_nome')
                )
		->join(
                    array('td' => $dbTerceiro), 'td.terceiro_id = df.terceiro_id_destinatario ',
                    array('terceiro_nome_destinatario' => 'td.terceiro_nome')
                );
        $rows = $dbDocumentoFiscal->fetchAll( $select );

        $data = array( 'rows' => array() );

        $date = new Zend_Date();

	if ( $rows->count() ) {

            $currency = new Zend_Currency();
            
	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_doc_fiscal_id,
		    'data' => array(
			++$key,
                        $row->fn_doc_fiscal_numero,
                        $date->set( $row->fn_doc_fiscal_data )->toString( 'dd/MM/yyyy' ),
                        $currency->setValue( $row->total_doc_fiscal )->toString(),
                        $row->terceiro_nome_remetente,
                        $row->terceiro_nome_destinatario
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
	$adapter = Zend_Db_Table::getDefaultAdapter();
        
	try {
        $translate = Zend_Registry::get('Zend_Translate');

        if(empty($this->_data["fn_doc_fiscal_item_descricao"])){

            $this->_message->addMessage( $translate->_('Nenhum item adicionado.'), App_Message::ERROR );
            return false;
        }
        
        foreach( $this->_data["fn_doc_fiscal_item_descricao"] as $key => $value){

            if( $this->_data["fn_doc_fiscal_item_valor"][$key] == 0 ){
                
                $this->_message->addMessage( $translate->_('Valor unitário do item não pode ser R$ 0,00.'), App_Message::ERROR );
                return false;
            }
        }
        
	    $dbDocumentoFiscal      = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
	    $dbDocumentoFiscalItens = App_Model_DbTable_Factory::get( 'DocumentoFiscalItens' );
	    $adapter->beginTransaction();

	    $where = array( 'terceiro_id_remetente = ?' => $this->_data['terceiro_id_remetente'],
                        'fn_doc_fiscal_numero = ?'  => $this->_data['fn_doc_fiscal_numero']);
		
	    if ( !$dbDocumentoFiscal->isUnique( $where, $this->_data['fn_doc_fiscal_id'] ) ) {
                
		$this->_message->addMessage( $translate->_('Documento Fiscal já cadastrado.'), App_Message::ERROR );
		return false;
	    }

            $itensBD = array();
            $result  = parent::_simpleSave( $dbDocumentoFiscal );
            if(empty($this->_data['fn_doc_fiscal_id'])){

                $docFiscalId = $result;
            }else{

                $docFiscalId  = $this->_data['fn_doc_fiscal_id'];
                $dataItensBD       = $this->listItensDocFiscal( $docFiscalId );

                foreach ( $dataItensBD as $item ) {

                    $itensBD[] = $item['fn_doc_fiscal_itens_id'];
                }
            }


            $itensTela = array();
            $dataItensTela = array();
            foreach( $this->_data["fn_doc_fiscal_item_descricao"] as $key => $value){


                $qtde       = $this->_data["fn_doc_fiscal_item_qtde"][$key];
                $valorUinit = $this->_data["fn_doc_fiscal_item_valor"][$key];
                $valorTotal = $qtde * $valorUinit;
                if( empty($this->_data["fn_doc_fiscal_itens_id"][$key]) ){

                    $dataTipo = array(
                                    "fn_doc_fiscal_id"              => $docFiscalId,
                                    "fn_doc_fiscal_item_descricao"  => $this->_data["fn_doc_fiscal_item_descricao"][$key],
                                    "fn_doc_fiscal_item_valor"      => $valorUinit,
                                    "fn_doc_fiscal_item_qtde"       => $qtde,
                                    "fn_doc_fiscal_item_total"      => $valorTotal
                                );

                    $dbDocumentoFiscalItens->insert( $dataTipo );
                }else{

                    $idxid                 = $this->_data['fn_lanc_cartao_tipo_id'][$key];
                    $itensTela[]           = $idxid;
                    $dataItensTela[$idxid] = array(
                                    "fn_doc_fiscal_id"              => $docFiscalId,
                                    "fn_doc_fiscal_item_descricao"  => $this->_data["fn_doc_fiscal_item_descricao"][$key],
                                    "fn_doc_fiscal_item_valor"      => $valorUinit,
                                    "fn_doc_fiscal_item_qtde"       => $qtde,
                                    "fn_doc_fiscal_item_total"      => $valorTotal
                                );
                }
            }

            $deleteItens = array_diff( $itensBD, $itensTela );
            $updateItens = array_diff( $itensBD, $deleteItens );

            foreach ($updateItens as $updateItemId) {

                $where = array(
                    'fn_doc_fiscal_id = ?'      => $docFiscalId,
                    'fn_doc_fiscal_itens_id = ?' => $updateItemId
                );

                $dbDocumentoFiscalItens->update( $dataItensTela[$updateItemId] , $where );
            }

            foreach ($deleteItens as $deleteItemId) {

                $where = array(
                    'fn_doc_fiscal_id = ?'      => $docFiscalId,
                    'fn_doc_fiscal_itens_id = ?' => $deleteItemId
                );

                $dbDocumentoFiscalItens->delete( $where );
            }

            $adapter->commit();

            return $result;

        } catch ( Exception $e ) {

            $adapter->rollBack();
            
            return false;

        }
    }

    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'fn_doc_fiscal_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'DocumentoFiscal' ), $where );
    }

    public function listItensDocFiscal( $id )
    {
	$dbDocumentoFiscalItens = App_Model_DbTable_Factory::get('DocumentoFiscalItens');

	$select = $dbDocumentoFiscalItens->select()
		->setIntegrityCheck( false )
		->from(
			array('idf' => $dbDocumentoFiscalItens), array('idf.*')
		)
                ->where( 'idf.fn_doc_fiscal_id = ?' , $id );

	return $dbDocumentoFiscalItens->fetchAll( $select );
    }
    
    public function lancamentosLigadosEmDocumentoFiscal( $id , $efetivado = false)
    {
	$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );

	$select = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('l' => $dbLancamento), array('l.*')
		)
                ->where( 'l.fn_doc_fiscal_id = ?' , $id );
        
        if( $efetivado )
            $select->where( 'l.fn_lancamento_efetivado = 1');

	$data = $dbLancamento->fetchAll( $select )->toArray();
        
        if(empty($data)){
            
            return false;
        }else{
            
            return $data;
        }
    }

    public function listaDocumentosFiscais( $filtros )
    {

        $dbDocumentoFiscal      = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        $dbTerceiro             = App_Model_DbTable_Factory::get( 'Terceiro' );
        $dbLancamento           = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbLancamentoCartao     = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
        $dbDocumentoFiscalItens = App_Model_DbTable_Factory::get( 'DocumentoFiscalItens' );
        
        
        $subSelectLanc = $dbLancamento->select()
                ->from(
                        $dbLancamento,
                        array( 'fn_doc_fiscal_id' ) 
                  )
                  ->where( 'fn_doc_fiscal_id IS NOT NULL' );
        
        $subSelectCart = $dbLancamentoCartao->select()
                ->from(
                        $dbLancamentoCartao,
                        array( 'fn_doc_fiscal_id' ) 
                  )
                ->where( 'fn_doc_fiscal_id IS NOT NULL' );

	$select = $dbDocumentoFiscal->select()
		->setIntegrityCheck( false )
		->from(
			array( 'df' => $dbDocumentoFiscal ), 
                        array( 
                                'df.fn_doc_fiscal_id',
                                'df.fn_doc_fiscal_numero',
                                'df.fn_doc_fiscal_data',
                                'total_doc_fiscal'  => '(' . new Zend_Db_Expr(
                                    $dbDocumentoFiscalItens->select()
                                        ->setIntegrityCheck( false )
                                        ->from(
                                            $dbDocumentoFiscalItens,
                                            array( 'SUM(fn_doc_fiscal_item_valor * fn_doc_fiscal_item_qtde)' )
                                        )
                                        ->where( 'fn_doc_fiscal_id = df.fn_doc_fiscal_id' )
                                    ) . ')'
                            )
		)
		->join(
            array('tr' => $dbTerceiro), 'tr.terceiro_id = df.terceiro_id_remetente ',
            array(
			'tr.terceiro_id',
			'terceiro_nome_remetente' => 'tr.terceiro_nome'
			)
                )
		->join(
                array('td' => $dbTerceiro), 'td.terceiro_id = df.terceiro_id_destinatario ',
                array('terceiro_nome_destinatario' => 'td.terceiro_nome')
                )
                ->where('df.fn_doc_fiscal_id NOT IN(?)' , $subSelectLanc)
                ->where('df.fn_doc_fiscal_id NOT IN(?)' , $subSelectCart);
        
        if(!empty($filtros['rem']))
            $select->where("tr.terceiro_nome LIKE '%".$filtros['rem']."%'"  );

        if(!empty($filtros['des']))
            $select->where("td.terceiro_nome LIKE '%".$filtros['des']."%'" );

        if(!empty($filtros['nro']))
            $select->where("df.fn_doc_fiscal_numero LIKE '%".$filtros['nro']."%'" );
        
        if(!empty($filtros['dat']))
            $select->where('df.fn_doc_fiscal_data = ?' , $filtros['dat'] );

	$rows = $dbDocumentoFiscal->fetchAll( $select );

        $data = array( 'rows' => array() );

        $date = new Zend_Date();

	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {

            $currency = new Zend_Currency();
                
            $data['rows'][] = array(
                'id' => array( $row->fn_doc_fiscal_id , $row->fn_doc_fiscal_numero, $row->terceiro_id, number_format($row->total_doc_fiscal, 2, ',', '') ),
                'data' => array(
                ++$key,
                            $row->fn_doc_fiscal_numero,
                            $date->set( $row->fn_doc_fiscal_data )->toString( 'dd/MM/yyyy' ),
                            $row->terceiro_nome_remetente,
                            $row->terceiro_nome_destinatario,
                            $currency->setValue( $row->total_doc_fiscal )->toString()
                )
            );
	    }
	}

	return $data;
    }

}
