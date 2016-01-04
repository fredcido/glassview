<?php

/**
 * 
 * @version $Id: Duplicata.php 1025 2013-10-16 14:33:55Z helion $
 */
class Model_Mapper_Duplicata extends App_Model_Mapper_Abstract
{
    
    public function fetchGrid()
    {

        $dbDuplicata       = App_Model_DbTable_Factory::get( 'Duplicata' );
        $dbLancamento      = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbTerceiro        = App_Model_DbTable_Factory::get( 'Terceiro' );
        $dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        $dbParcela         = App_Model_DbTable_Factory::get( 'Parcela' );
        
        
        $subSelect = $dbParcela->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('sp' => $dbParcela), array('COUNT(1)')
                )
                ->join(
                        array('sl' => $dbLancamento), 'sl.fn_lancamento_id = sp.fn_lancamento_id', array()
                )
                ->where('sp.fn_duplicata_id = d.fn_duplicata_id')
                ->where('sl.fn_lancamento_efetivado  = ?', 1);
        
        $select = $dbDuplicata->select()
                ->setIntegrityCheck( false )
                ->from(
                        array( 'd' => $dbDuplicata), 
                        array( 'd.*')
                )
                ->join(
                        array( 'l' => $dbLancamento), 'l.fn_lancamento_id = d.fn_lancamento_id', 
                        array( 
                                'l.fn_lancamento_data',
                                'parcelas_pagas'     => new Zend_Db_Expr('(' . $subSelect . ')') 
                            )
                        )
                ->join(
                        array( 't' => $dbTerceiro), 't.terceiro_id = l.terceiro_id', 
                        array( 't.terceiro_nome' )
                        )
                ->join(
                        array( 'df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id', 
                        array( 'df.fn_doc_fiscal_numero' )
            );


        $rows = $dbDuplicata->fetchAll( $select );

        $data = array( 'rows' => array() );

        $date = new Zend_Date();
        
	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {
                
            $currency = new Zend_Currency();
            
            $situacao = 'Quitado';
            if($row->parcelas_pagas != $row->fn_duplicata_parcelas){
                
                $situacao = 'Pago '.$row->parcelas_pagas.' de '.$row->fn_duplicata_parcelas;
            }
            
            $data['rows'][] = array(
                'id' => $row->fn_duplicata_id,
                'data' => array(
                    ++$key,
                    $date->set( $row->fn_lancamento_data )->toString( 'dd/MM/yyyy' ),
                    $row->terceiro_nome,
                    $row->fn_doc_fiscal_numero,
                    $situacao,
                    $row->fn_duplicata_parcelas,
                    $this->_getDuplicataTipo($row->fn_duplicata_tipo),
                    $currency->setValue( $row->fn_duplicata_total )->toString()
                )
            );
	    }
	}

	return $data;
    }
    
    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getDuplicataTipo( $type )
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $optDuplicataTipo = array(
            'E' => $translate->_('Entrada'),
            'S' => $translate->_('Saída')
        );

	return ( empty( $optDuplicataTipo[$type] ) ? '-' : $optDuplicataTipo[$type] );
    }
    
   /**
     *
     * @return boolean
     */
    public function save()
    {
        
	$adapter = Zend_Db_Table::getDefaultAdapter();

	try {
        	    
	    $dbDuplicata  = App_Model_DbTable_Factory::get( 'Duplicata' );
	    $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
	    $dbParcela    = App_Model_DbTable_Factory::get( 'Parcela' );
            
            parent::setValidators( array('_validDocumentoFiscal') );

            if ( parent::isValid() ) {
                
                $adapter->beginTransaction();

                if(empty($this->_data['fn_duplicata_id'])){

                    $dataLancDupl = array(
                                    "fn_lancamento_id"         => null,
                                    "terceiro_id"              => $this->_data["terceiro_id"],
                                    "fn_conta_id"              => $this->_data["fn_conta_id"],
                                    "fn_lancamento_data"       => Zend_Date::now()->toString('yyyy-MM-dd'),
                                    "fn_lancamento_valor"      => $this->_data["fn_duplicata_total"],
                                    "fn_doc_fiscal_id"         => $this->_data["fn_doc_fiscal_id"],
                                    "fn_lancamento_status"     => 'A',
                                    "fn_lancamento_efetivado"  => 0,
                                    "fn_lancamento_estorno"    => 0,
                                    "fn_lancamento_ajuste"     => 0,
                                    "fn_lancamento_tipo"       => ( $this->_data["fn_duplicata_tipo"] == 'S' ? 'D' : 'C')
                                );


                    $idLancDupl = $dbLancamento->insert( $dataLancDupl );
                    $this->_data['fn_lancamento_id'] = $idLancDupl;
                }else{

                    $dataLancDupl = array(
                                    "fn_lancamento_id"        => $this->_data["fn_lancamento_id"],
                                    "terceiro_id"             => $this->_data["terceiro_id"],
                                    "fn_conta_id"             => $this->_data["fn_conta_id"],
                                    "fn_lancamento_valor"     => $this->_data["fn_duplicata_total"],
                                    "fn_doc_fiscal_id"        => $this->_data["fn_doc_fiscal_id"],
                                    "fn_lancamento_status"    => 'A',
                                    "fn_lancamento_efetivado" => 0,
                                    "fn_lancamento_estorno"   => 0,
                                    "fn_lancamento_ajuste"    => 0,
                                    "fn_lancamento_tipo"      => ( $this->_data["fn_duplicata_tipo"] == 'S' ? 'D' : 'C')
                                );

                    $where = array( 'fn_lancamento_id = ?'  => $this->_data["fn_lancamento_id"]);

                    $idLancDupl = $dbLancamento->update( $dataLancDupl , $where );
                }

                $result = parent::_simpleSave( $dbDuplicata );

                $parcelasBd = array();
                if(!empty($this->_data['fn_duplicata_id'])){

                    $parcelasBd = $this->listaParcelasDuplicata( $this->_data['fn_duplicata_id'] );
                }

                $somaParcelas = (float)0;
                $dataPacela   = array( 'fn_parcela_ref' => 0 );
                foreach ($this->_data['fn_lancamento_valor'] as $key => $parcela){

                    if(!empty($this->_data['fn_lancamento_valor'][$key])){

                        $dataPacela['fn_parcela_ref']++;

                        $dataLancDupl['fn_lancamento_data']  = $this->_data['fn_lancamento_data'][$key];
                        $dataLancDupl['fn_lancamento_valor'] = $this->_data['fn_lancamento_valor'][$key];
                        $dataLancDupl['fn_lancamento_tipo'] = ( $this->_data["fn_duplicata_tipo"] == 'S' ? 'D' : 'C');
                        
                        if(!empty($this->_data['id_conta'][$key]))
                            $dataLancDupl['fn_conta_id'] = $this->_data['id_conta'][$key];

                        unset($dataLancDupl["fn_lancamento_id"]);
                        if( count($parcelasBd) < $dataPacela['fn_parcela_ref'] ){

                            $idLancDupl = $dbLancamento->insert( $dataLancDupl );

                            $dataPacela['fn_lancamento_id']      = $idLancDupl;
                            $dataPacela['fn_duplicata_id']       = $result;
                            $dataPacela['fn_parcela_vencimento'] = $this->_data['fn_lancamento_data'][$key];
                            $dataPacela['fn_parcela_valor']      = $this->_data['fn_lancamento_valor'][$key];
                            
                            $dbParcela->insert( $dataPacela );
                        }else{

                            $whereLanca   = array();
                            $whereParcela = array();
                            foreach ($parcelasBd as $row) {
                                if($row->fn_parcela_ref == $dataPacela['fn_parcela_ref']){

                                    $whereLanca = array(
                                            'fn_lancamento_id = ?' => $row->fn_lancamento_id,
                                        );

                                    $whereParcela = array(
                                            'fn_duplicata_id = ?' => $row->fn_duplicata_id,
                                            'fn_lancamento_id = ?' => $row->fn_lancamento_id,
                                        );
                                }
                            }

                            if(empty($parcelasBd[$this->_data['id_parcela'][$key]])){
                                
                                $dbLancamento->update( $dataLancDupl , $whereLanca  );
                            }  else {
                                
                                $dataLancDuplbd = $parcelasBd[$this->_data['id_parcela'][$key]]->toArray();
                                $dataLancDuplbd['fn_lancamento_data']  = $this->_data['fn_lancamento_data'][$key];
                                $dataLancDuplbd['fn_lancamento_valor'] = $this->_data['fn_lancamento_valor'][$key];
                                $dataLancDuplbd['fn_lancamento_tipo']  = ( $this->_data["fn_duplicata_tipo"] == 'S' ? 'D' : 'C');
                                
                                unset($dataLancDuplbd['fn_duplicata_id']);
                                unset($dataLancDuplbd['fn_parcela_ref']);
                                unset($dataLancDuplbd['fn_parcela_vencimento']);
                                unset($dataLancDuplbd['fn_parcela_valor']);
                                if(!empty($this->_data['id_conta'][$key]))
                                    $dataLancDuplbd['fn_conta_id'] = $this->_data['id_conta'][$key];

                                $dbLancamento->update( $dataLancDuplbd , $whereLanca  );
                            }

                            $dataPacela['fn_parcela_vencimento'] = $this->_data['fn_lancamento_data'][$key];
                            $dataPacela['fn_parcela_valor']      = $this->_data['fn_lancamento_valor'][$key];

                            $dbParcela->update( $dataPacela , $whereParcela  );
                        }

                        $somaParcelas = $somaParcelas + $this->_data['fn_lancamento_valor'][$key];
                    }
                }

                foreach ( $parcelasBd as $rowParcela ) {

                    if( $rowParcela->fn_parcela_ref > $dataPacela['fn_parcela_ref'] ){

                        $where = array( 
                                'fn_lancamento_id = ?' => $rowParcela->fn_lancamento_id,
                                'fn_parcela_ref = ?'  => $rowParcela->fn_parcela_ref,
                                'fn_duplicata_id = ?' => $rowParcela->fn_duplicata_id
                                );

                        $dbParcela->delete( $where );

                        $dbLancamento->delete( array( 'fn_lancamento_id = ?' => $rowParcela->fn_lancamento_id)  );
                    }
                }

                $adapter->commit();
                return $result;
            }
            
        } catch ( Exception $e ) {
            
            var_dump($e);
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

    $dbDuplicata       = App_Model_DbTable_Factory::get( 'Duplicata' );
    $dbLancamento      = App_Model_DbTable_Factory::get( 'Lancamento' );
    $dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );

    $select = $dbDuplicata->select()
		->setIntegrityCheck( false )
		->from(
                    array('d' => $dbDuplicata), array('d.*')
		)
		->join(
                    array('l' => $dbLancamento), 'l.fn_lancamento_id = d.fn_lancamento_id', 
                    array( 
                        'l.fn_lancamento_data',
                        'l.terceiro_id',
                        'l.fn_conta_id',
                        'l.fn_doc_fiscal_id'
                        )
                )
                ->join(
                    array('df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id', 
                    array('df.fn_doc_fiscal_numero' )
                )
                ->where('d.fn_duplicata_id  = ?' , $this->_data['id'] );

        return $dbDuplicata->fetchRow( $select );

    }
    
    /**
    *
    * @return App_Model_DbTable_Row_Abstract
    */
    public function listaParcelasDuplicata($duplicataId, $efetivados = false)
    {

        $dbParcela    = App_Model_DbTable_Factory::get( 'Parcela' );
        $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );

        $select = $dbParcela->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('p' => $dbParcela), array('p.*')
                )
                ->join(
                        array('l' => $dbLancamento),'l.fn_lancamento_id = p.fn_lancamento_id', 
                        array('l.*')
                )
                ->where('p.fn_duplicata_id  = ?', $duplicataId);

        if ($efetivados)
            $select->where('l.fn_lancamento_efetivado  = ?', 1);

        $data = $dbParcela->fetchAll($select);
        
        if(!empty($data)){
            
            foreach ($data as $value) {
                
                $dados[$value->fn_lancamento_id] = $value;
            }
            
        }else{
            
            $dados = false;
        }
        
        return $dados;
    }
    
    /**
     * Verifica se os dados do lancamento estao de acordo com o documento fiscal selecionado
     * 
     * @access protected
     * @return bool
     */
    protected function _validDocumentoFiscal()
    {
        $translate = Zend_Registry::get('Zend_Translate');
        if (empty($this->_data['fn_lancamento_valor'])){
            $this->_message->addMessage($translate->_('Nenhuma parcelamento realizado'), App_Message::ERROR);
            return false;
        }
        
        $totalLancamentos = array_sum($this->_data['fn_lancamento_valor']);
        
        if ($this->_data["fn_duplicata_total"] != "".$totalLancamentos){
            $this->_message->addMessage($translate->_('Parcelamento incorreto, a soma das parcelas não é igual ao valor total'), App_Message::ERROR);
            return false;
        }
        
        $dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        $dbDocFiscalItens  = App_Model_DbTable_Factory::get( 'DocumentoFiscalItens');
        $dbDuplicata       = App_Model_DbTable_Factory::get( 'Duplicata' );
        $dbLancamento      = App_Model_DbTable_Factory::get( 'Lancamento');

        
        $select1 = $dbDuplicata->select()
            ->setIntegrityCheck( false )
            ->from( 
                    array('d' => $dbDuplicata), array( 'd.fn_duplicata_id' ) 
             )
            ->join(
                array( 'l' => $dbLancamento), 'l.fn_lancamento_id = d.fn_lancamento_id', 
                array( 'l.fn_lancamento_data' )
            );
        
        $select1->where('d.fn_duplicata_id  <> ?' , $this->_data['fn_duplicata_id'] );
        $select1->where('l.fn_doc_fiscal_id  = ?' , $this->_data['fn_doc_fiscal_id'] );

        $rows = $dbDuplicata->fetchAll( $select1 )->toArray();
        
        if ( !empty($rows) ) {

            $this->_message->addMessage( 'Duplicata já cadastrada.', App_Message::ERROR );
            return false;
        }

        $subSelect = $dbDocumentoFiscal->select()
                ->from(
                        array('df' => $dbDocumentoFiscal), array('terceiro_id_remetente')
                )
                ->where('df.fn_doc_fiscal_id = dfi.fn_doc_fiscal_id');

        $select = $dbDocFiscalItens->select()
                ->from(
                        array('dfi' => $dbDocFiscalItens), array(
                            'valor' => new Zend_Db_Expr('ROUND(SUM(fn_doc_fiscal_item_valor * fn_doc_fiscal_item_qtde), 2)'),
                            'terceiro_id' => new Zend_Db_Expr('(' . $subSelect . ')')
                        )
                )
                ->where('dfi.fn_doc_fiscal_id = ?', $this->_data['fn_doc_fiscal_id']);

        $row = $dbDocFiscalItens->fetchRow($select);

        $fn_duplicata_total = $this->_data['fn_duplicata_total'];
        
        if ( $row->valor != $fn_duplicata_total ) {

            $this->_message->addMessage(
                    'O valor R$ ' . number_format($fn_duplicata_total, 2, ',', '') .
                    ' não corresponde com documento fiscal, R$ ' .
                    number_format( $row->valor , 2, ',', ''), App_Message::WARNING
            );

            return false;
        }

        return true;
    }
    
    /**
    *
    * @return App_Model_DbTable_Row_Abstract
    */
    public function verificaLancamentoDuplicata( $lancamento_id )
    {
        $dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbParcela    = App_Model_DbTable_Factory::get( 'Parcela' );
        $dbDuplicata  = App_Model_DbTable_Factory::get( 'Duplicata' );

        $select = $dbLancamento->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('l' => $dbLancamento),
                        array('l.*')
                )
                ->join(
                        array('p' => $dbParcela), 'p.fn_lancamento_id = l.fn_lancamento_id',
                        array(
                                'p.fn_parcela_valor',
                                'p.fn_parcela_ref'
                            )
                )
                ->join(
                        array('d' => $dbDuplicata), 'd.fn_duplicata_id = p.fn_duplicata_id',
                        array(
                                'lancamento_duplicata_id' => 'd.fn_lancamento_id',
                                'd.fn_duplicata_parcelas'
                            )
                )
                ->where('l.fn_lancamento_id  = ?', $lancamento_id);

        $rows = $dbLancamento->fetchAll($select)->toArray();
        
        if(empty($rows)){
            
            return false;
        }else{
            
            return $rows[0];
        }
        
    }
 /**
     * 
     * @access public
     * @return Zend_Db_Table_Rowset 
     */
    public function isDeleteDuplicata()
    {
        $dbDuplicata  = App_Model_DbTable_Factory::get( 'Duplicata' );
	$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbParcela    = App_Model_DbTable_Factory::get( 'Parcela' );

	$selectParcelas= $dbParcela->select()
	    ->from(
		array( 'p' => $dbParcela ),
		array( 'fn_lancamento_id' )
	    )->where(  'p.fn_duplicata_id = ?', $this->_data['fn_duplicata_id'] );
        
	$selectPago = $dbLancamento->select()
	    ->from(
		array( 'l' => $dbLancamento ),
		array( 'count(1)' )
	    )
            ->where(  'l.fn_lancamento_efetivado = ?' , 1 )
            ->where(  'l.fn_lancamento_id IN(?)' , new Zend_Db_Expr( $selectParcelas ) );

	$select = $dbDuplicata->select()
		->from(
			array('d' => $dbDuplicata), 
                        array(
                            'fn_duplicata_id',
                            'fn_duplicata_parcelas',
                            'parcelas_pagas' => new Zend_Db_Expr( '('.$selectPago.')' ),
                            'fn_duplicata_quitada'
			)
		)
		->where( 'd.fn_duplicata_id = ?', $this->_data['fn_duplicata_id'] );

	$rows = $dbLancamento->fetchRow( $select )->toArray();

        if( empty($rows['parcelas_pagas']) && 
            $this->_session->acl->isAllowedToolbar( App_Plugins_Acl::getIdentifier( '/financeiro/duplicata/', 'Deletar' )) ){
            
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
        if(!$this->isDeleteDuplicata()){
            
            $this->_message->addMessage( 'Duplicata não pode ser removida!', App_Message::ERROR );
            return false;
        }
        
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $adapter->beginTransaction();
        
        try {

            $dbLancamentoProjeto = App_Model_DbTable_Factory::get( 'LancamentoProjeto' );
            $dbLancamento        = App_Model_DbTable_Factory::get( 'Lancamento' );
            $dbParcela           = App_Model_DbTable_Factory::get( 'Parcela' );
            $dbDuplicata         = App_Model_DbTable_Factory::get( 'Duplicata' );

            
            $selectDuplicata = $dbDuplicata->select()
                ->from(
                    array( 'd' => $dbDuplicata ),
                    array( 'fn_lancamento_id' )
                )->where(  'd.fn_duplicata_id = ? ', (int)$this->_data['fn_duplicata_id'] );

            $rowsDuplicata = $dbDuplicata->fetchAll( $selectDuplicata )->toArray();
            
            $selectParcela = $dbParcela->select()
                ->from(
                    array( 'p' => $dbParcela ),
                    array( 'fn_lancamento_id' )
                )->where(  'p.fn_duplicata_id = ? ', (int)$this->_data['fn_duplicata_id'] );

            $rowsParcela = $dbParcela->fetchAll( $selectParcela )->toArray();
            
            $rows = array_merge($rowsDuplicata,$rowsParcela);

            $dbParcela->delete(   array( 'fn_duplicata_id = ?' => (int)$this->_data['fn_duplicata_id'] ) );
            $dbDuplicata->delete( array( 'fn_duplicata_id = ?' => (int)$this->_data['fn_duplicata_id'] ) );
            
            foreach ($rows as $value) {
                
                $dbLancamentoProjeto->delete( array( 'fn_lancamento_id = ?' => (int)$value["fn_lancamento_id"] ) );
            }
            
            foreach ($rows as $value) {
                
                $dbLancamento->delete( array( 'fn_lancamento_id = ?' => (int)$value["fn_lancamento_id"] ) );
            }
            
            $this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );

            $adapter->commit();
            return true;
        } catch ( Exception $e ) {

            var_dump( $e);
            $adapter->rollBack();
            $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );

            return false;

        } 

    }
}