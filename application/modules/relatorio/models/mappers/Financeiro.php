<?php

/**
 * 
 * @version $Id: Financeiro.php 962 2013-09-10 17:27:03Z helion $
 */
class Relatorio_Model_Mapper_Financeiro extends App_Model_Mapper_Abstract
{
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function conta()
    {
	$dbLancamento        = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbConta             = App_Model_DbTable_Factory::get( 'Conta' );
	$dbLancamentoProjeto = App_Model_DbTable_Factory::get( 'LancamentoProjeto' );
	$dbDocumentoFiscal   = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
	$dbTerceiro          = App_Model_DbTable_Factory::get( 'Terceiro' );
	$dbProjeto           = App_Model_DbTable_Factory::get( 'Projeto' );
	$dbTipoLancamento    = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	$dbDuplicata         = App_Model_DbTable_Factory::get( 'Duplicata' );
	$dbFatura            = App_Model_DbTable_Factory::get( 'Fatura' );
	
	 $subSelect = $dbDuplicata->select()
  				  ->from(
					array( 'd' => $dbDuplicata ),
					array( 'fn_lancamento_id' )
				  );

	
	$select = $dbLancamento->select()
			       ->from( 
				    array ( 'l' => $dbLancamento ),
				    array(
					'fn_lancamento_id',
					'fn_lancamento_data',
					'fn_lancamento_dtefetivado',
					'fn_lancamento_obs',
					'fn_lancamento_valor',
					'fn_lancamento_tipo',
					'fn_lancamento_trans',
					'fn_lancamento_estorno'
				    )
				)
			       ->setIntegrityCheck( false )
			       ->join(
				    array( 'c' => $dbConta ),
				    'c.fn_conta_id = l.fn_conta_id',
				    array( 'fn_conta_descricao' )
			       )
			       ->joinLeft(
				    array( 'df' => $dbDocumentoFiscal ),
				    'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id',
				    array( 'fn_doc_fiscal_numero' )
			       )
			       ->joinLeft(
				    array( 'tr' => $dbTerceiro ),
				    'tr.terceiro_id = df.terceiro_id_remetente',
				    array( 'terceiro_nome_remetente' =>  'terceiro_nome')
			       )
			       ->joinLeft(
				    array( 'lp' => $dbLancamentoProjeto ),
				    'lp.fn_lancamento_id = l.fn_lancamento_id',
				    array( 'fn_lanc_projeto_valor' )
			       )
			       ->joinLeft(
				    array( 'p' => $dbProjeto ),
				    'p.projeto_id = lp.projeto_id',
				    array( 'projeto_id','projeto_nome' )
			       )
			       ->joinLeft(
				    array( 'tl' => $dbTipoLancamento ),
				    'tl.fn_tipo_lanc_id = lp.fn_tipo_lanc_id',
				    array( 'fn_tipo_lanc_cod', 'fn_tipo_lanc_desc' )
			       )
			       ->joinLeft(
				    array( 'fat' => $dbFatura ),
				    'fat.fn_lancamento_id = l.fn_lancamento_id',
				    array( 'fn_cc_fat_id' )
			       )
			       ->where( 'l.fn_lancamento_id NOT IN(?)', $subSelect );
			       
	

	if ( !empty( $this->_data['fn_conta_id'] ) )
	    $select->where( 'l.fn_conta_id = ?', $this->_data['fn_conta_id'] );
	
	if ( !empty( $this->_data['fn_tipo_lanc_id'] ) )
	    $select->where( 'lp.fn_tipo_lanc_id = ?', $this->_data['fn_tipo_lanc_id'] );
	
	if ( !empty( $this->_data['projeto_id'] ) )
	    $select->where( 'lp.projeto_id = ?', $this->_data['projeto_id'] );
	
	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_dtefetivado ) >= ?', $this->_data['rel_data_ini'] );
	
	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_dtefetivado ) <= ?', $this->_data['rel_data_fim'] );
        
	if ( !empty( $this->_data['rel_lanca_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_lanca_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_data ) >= ?', $this->_data['rel_lanca_data_ini'] );
	
	if ( !empty( $this->_data['rel_rel_lanca_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_rel_lanca_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_data ) <= ?', $this->_data['rel_rel_lanca_data_fim'] );
        
	if ( isset( $this->_data['fn_lancamento_efetivado'] ) ){
            
	    $select->where( 'l.fn_lancamento_efetivado = ?', (int)$this->_data['fn_lancamento_efetivado'] );
        }else{
            
            $select->where( 'l.fn_lancamento_efetivado = ?', 1);
        }
        $select->order( array( 'l.fn_lancamento_data') );

	return $dbLancamento->fetchAll( $select );
    }
    
    /**
     *
     * @param Zend_Db_Table_Rowset $data
     * @return array 
     */
    public function agrupaLancamentos( $data )
    {
	$dataFinal = array(
	    'total' =>	0,
	    'lanc'  =>	array()
	);
	
	foreach ( $data as $row ) {
	    
	    if ( empty( $dataFinal['lanc'][ $row->fn_lancamento_id ] ) ) {
		
		$dataFinal['lanc'][ $row->fn_lancamento_id ] = array(
		    'data'  => $row,
		    'itens' => array()
		);
		
		if ( 'C' == $row->fn_lancamento_tipo ){
                    
		    $dataFinal['total'] += (float)$row->fn_lancamento_valor;
                }else{
                    
		    $dataFinal['total'] -= (float)$row->fn_lancamento_valor;
                }
	    }
	    
	    $dataFinal['lanc'][ $row->fn_lancamento_id ]['itens'][] = $row; 
	}
		
	return $dataFinal;
    }
    
    /**
     *
     * @param Zend_Db_Table_Rowset $data
     * @return array 
     */
    public function agrupaLancamentosRelatorioContaProjeto( $data )
    {
        
        $faturas    = array();
        $dataFinal  = array();        
        $totalFinal = 0;
        $totalFinalProjeto = 0;
	foreach ( $data as $row ) {
	    

            $idxProj  = (empty($row->projeto_id      ) ? 'NaN' : $row->projeto_id);
            $idxLanc  = (empty($row->fn_lancamento_id) ? 'NaN' : $row->fn_lancamento_id);
            $idxTLanc = (empty($row->fn_tipo_lanc_cod) ? 'NaN' : $row->fn_tipo_lanc_cod);

            if( !empty($row->fn_cc_fat_id) && !empty($this->_data['detalha_cartao']) ){
                
                $faturas[] = $row->fn_cc_fat_id;
            }else{
            
                if ( empty( $dataFinal[$idxProj]['data'][$idxTLanc][$idxLanc] ) ) {

                    $dataFinal[$idxProj]['data'][$idxTLanc][$idxLanc] = $row;

                    if ( 'C' == $row->fn_lancamento_tipo ){

                        $totalFinal += (float)$row->fn_lancamento_valor;
                        $totalFinalProjeto += (float)$row->fn_lanc_projeto_valor;
                    }else{

                        $totalFinal -= (float)$row->fn_lancamento_valor;
                        $totalFinalProjeto -= (float)$row->fn_lanc_projeto_valor;
                    }
                }else{

                    //die('Error na consulta, ids duplicados!');
                }
            }
	    
	}

        if(!empty($faturas)){
            
            $dataFatura = $this->LancamentoCartaoRelatorioContaProjeto( $faturas );
            foreach ( $dataFatura as $row ) {
                
                $idxProj  = (empty($row->projeto_id      ) ? 'NaN' : $row->projeto_id);
                $idxLanc  = (empty($row->fn_lancamento_id) ? 'NaN' : $row->fn_lancamento_id);
                $idxTLanc = (empty($row->fn_tipo_lanc_cod) ? 'NaN' : $row->fn_tipo_lanc_cod);
                
                if ( empty( $dataFinal[$idxProj]['data'][$idxTLanc][$idxLanc] ) ) {

                    $dataFinal[$idxProj]['data'][$idxTLanc][$idxLanc] = $row;

                    if ( 'C' == $row->fn_lancamento_tipo ){

                        $totalFinal += (float)$row->fn_lancamento_valor;
                        $totalFinalProjeto += (float)$row->fn_lanc_projeto_valor;
                    }else{

                        $totalFinal -= (float)$row->fn_lancamento_valor;
                        $totalFinalProjeto -= (float)$row->fn_lanc_projeto_valor;
                    }
                }else{

                    //die('Error na consulta, ids duplicados!');
                }
            }
        }
        
//        foreach ($dd[0] as $key => $value) {
//            
//            $ddd[0][] = $key;
//            echo $key.'<br>';
//        }
//
//        echo '<br>----<br><br>';
//        foreach ($dd[1] as $key => $value) {
//            
//            $ddd[1][] = $key;
//            echo $key.'<br>';
//        }

	return array( 
            'lanc' => $dataFinal , 
            'total' => $totalFinal, 
            'totalprojeto' => $totalFinalProjeto
            );
    }
    
    /**
    *
    * @return Zend_Db_Table_Rowset
    */
    public function LancamentoCartaoRelatorioContaProjeto(array $fatura )
    {
	$dbFatura               = App_Model_DbTable_Factory::get( 'Fatura' );
	$dbLancamentoFatura     = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
	$dbLancamento           = App_Model_DbTable_Factory::get( 'Lancamento' );
	$dbConta                = App_Model_DbTable_Factory::get( 'Conta' );
        $dbLancamentoCartao     = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
	$dbDocumentoFiscal      = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
	$dbTerceiro             = App_Model_DbTable_Factory::get( 'Terceiro' );
	$dbProjeto              = App_Model_DbTable_Factory::get( 'Projeto' );
	$dbTipoLancamento       = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	$dbLancamentoCartaoTipo = App_Model_DbTable_Factory::get( 'LancamentoCartaoTipo' );

	$select = $dbFatura->select()
		->setIntegrityCheck( false )
                ->from( 
                        array ( 'fat' => $dbFatura ),
                        array ( 'fn_cc_fat_id' )
                )
                ->join(
                    array( 'l' => $dbLancamento ), 'l.fn_lancamento_id = fat.fn_lancamento_id',
                    array( 'fn_lancamento_dtefetivado' )
                )
                ->join(
                    array( 'c' => $dbConta ),
                    'c.fn_conta_id = l.fn_conta_id',
                    array( 'fn_conta_descricao' )
                )
		->join(
                    array( 'lf' => $dbLancamentoFatura ), 'lf.fn_cc_fat_id = fat.fn_cc_fat_id',
                    array( )
                )
		->join(
                    array( 'lc' => $dbLancamentoCartao ), 'lc.fn_lanc_cartao_id = lf.fn_lanc_cartao_id',
                    array(
                        'fn_lancamento_id' => 'CONCAT(l.fn_lancamento_id,lc.fn_lanc_cartao_id)',
                        'fn_lancamento_obs' => 'CONCAT(lc.fn_lanc_cartao_desc,"(Ref. Fatura Cartão - ",l.fn_lancamento_obs,")")',
                        'fn_lancamento_valor' => 'lc.fn_lanc_cartao_valor',
                        'fn_lancamento_data' =>'lc.fn_lanc_cartao_data',
                        'lc.fn_lanc_cartao_status',
                        'fn_lancamento_tipo' => '(Select "D")',
                        'fn_lancamento_trans' => '(Select 0)',
                        'fn_lancamento_estorno' => '(Select 0)'
                    )
                )
                ->joinLeft(
                    array( 'df' => $dbDocumentoFiscal ),
                    'df.fn_doc_fiscal_id = lc.fn_doc_fiscal_id',
                    array( 'fn_doc_fiscal_numero' )
               )
               ->joinLeft(
                    array( 'tr' => $dbTerceiro ),
                    'tr.terceiro_id = df.terceiro_id_remetente',
                    array( 'terceiro_nome_remetente' =>  'terceiro_nome')
               )
               ->joinLeft(
                    array( 'lp' => $dbLancamentoCartaoTipo ),
                    'lp.fn_lanc_cartao_id = lc.fn_lanc_cartao_id',
                    array( 'fn_lanc_projeto_valor' => 'fn_lanc_cc_tipo_valor' )
               )->joinLeft(
                    array( 'tl' => $dbTipoLancamento ),
                    'tl.fn_tipo_lanc_id = lp.fn_tipo_lanc_id',
                    array( 'fn_tipo_lanc_cod', 'fn_tipo_lanc_desc' )
               )
               ->joinLeft(
                    array( 'p' => $dbProjeto ),
                    'p.projeto_id = tl.projeto_id',
                    array( 'projeto_id','projeto_nome' )
               )
               ->where( 'fat.fn_cc_fat_id IN(?)', $fatura );

	return $dbFatura->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function Transferencia()
    {
	$dbLancamento = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbConta      = App_Model_DbTable_Factory::get( 'Conta' );
        
	$select = $dbLancamento->select()
			       ->from( 
				    array ( 'ld' => $dbLancamento ),
				    array(
					'fn_lancamento_id',
					'fn_lancamento_data',
					'fn_lancamento_dtefetivado',
					'fn_lancamento_valor',
                                        'conta_credito'  => '(' . new Zend_Db_Expr(
                                            $dbConta->select()
                                                ->setIntegrityCheck( false )
                                                ->from(
                                                    $dbConta,
                                                    array( 'fn_conta_descricao' )
                                                )
                                                ->where( 'fn_conta_id = ld.fn_conta_id' )
                                            ) . ')',
                                        'conta_debito' => '(' . new Zend_Db_Expr(
                                            $dbConta->select()
                                                ->setIntegrityCheck( false )
                                                ->from(
                                                    $dbConta,
                                                    array( 'fn_conta_descricao' )
                                                )
                                                ->where( 'fn_conta_id = lc.fn_conta_id' )
                                            ) . ')'
                                        )
				    )
			       ->setIntegrityCheck( false )
			       ->join(
				    array ( 'lc' => $dbLancamento ),
				    'lc.fn_lancamento_id = ld.fn_lancamento_anterior',
				    array()
			       )
			       ->order( array( 'ld.fn_lancamento_data DESC') );
	        
	if (!empty($this->_data['fn_conta_id_debito']))
            $select->where('ld.fn_conta_id = ?', $this->_data['fn_conta_id_debito']);

        if (!empty($this->_data['fn_conta_id_credito']))
            $select->where('lc.fn_conta_id = ?', $this->_data['fn_conta_id_credito']);

        if (!empty($this->_data['rel_data_ini']) && Zend_Date::isDate($this->_data['rel_data_ini'], 'yyyy-MM-dd'))
            $select->where('DATE( lc.fn_lancamento_data ) >= ?', $this->_data['rel_data_ini']);

        if (!empty($this->_data['rel_data_fim']) && Zend_Date::isDate($this->_data['rel_data_fim'], 'yyyy-MM-dd'))
            $select->where('DATE( ld.fn_lancamento_data ) <= ?', $this->_data['rel_data_fim']);

        return $dbLancamento->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function Cheque()
    {
        $dbCheque   = App_Model_DbTable_Factory::get( 'Cheque' );
	$dbConta    = App_Model_DbTable_Factory::get( 'Conta' );
	$dbBanco    = App_Model_DbTable_Factory::get( 'Banco' );
	$dbTerceiro = App_Model_DbTable_Factory::get( 'Terceiro' );

	$select = $dbCheque->select()
		->setIntegrityCheck( false )
		->from(
			array('ch' => $dbCheque), 
                        array(
                                'ch.fn_cheque_id',
                                'ch.fn_conta_id',
                                'ch.terceiro_id',
                                'ch.fn_cheque_numero',
                                'ch.fn_cheque_valor',
                                'ch.fn_cheque_data',
                                'ch.fn_cheque_para',
                                'ch.fn_cheque_situacao'
                            )
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
        
	if ( !empty( $this->_data['fn_cheque_situacao'] ) )
	    $select->where( 'ch.fn_cheque_situacao = ?', $this->_data['fn_cheque_situacao'] );
        
	if ( !empty( $this->_data['terceiro_id'] ) )
	    $select->where( 'ch.terceiro_id = ?', $this->_data['terceiro_id'] );
        
	if ( !empty( $this->_data['fn_conta_id'] ) )
	    $select->where( 'ch.fn_conta_id = ?', $this->_data['fn_conta_id'] );
        
	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( ch.fn_cheque_para ) >= ?', $this->_data['rel_data_ini'] );
	
	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( ch.fn_cheque_para ) <= ?', $this->_data['rel_data_fim'] );
        
	return $dbCheque->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function Duplicata()
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
                        array('sl' => $dbLancamento), 'sl.fn_lancamento_id = sp.fn_lancamento_id', 
                        array()
                )
                ->where('sp.fn_duplicata_id = d.fn_duplicata_id')
                ->where('sl.fn_lancamento_efetivado  = ?', 1);
        
        $dataQuitacao = $dbParcela->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('sp' => $dbParcela),
                        array()
                )
                ->join(
                        array('sl' => $dbLancamento), 'sl.fn_lancamento_id = sp.fn_lancamento_id', 
                        array('MAX(sl.fn_lancamento_dtefetivado)')
                )
                ->where('sp.fn_duplicata_id = d.fn_duplicata_id')
                ->where('sl.fn_lancamento_dtefetivado IS NOT NULL');
        
	$select = $dbDuplicata->select()
		->setIntegrityCheck( false )
		->from(
                   array('d' => $dbDuplicata),
                   array(
                        'd.fn_duplicata_id',
                        'd.fn_lancamento_id',
                        'd.fn_duplicata_total',
                        'd.fn_duplicata_parcelas',
                        'd.fn_duplicata_tipo',
                        'd.fn_duplicata_quitada'
                       )
		)
		->join(
                    array('l' => $dbLancamento), 'l.fn_lancamento_id = d.fn_lancamento_id',
                    array(
                            'l.fn_lancamento_data',
                            'parcelas_pagas'     => new Zend_Db_Expr('(' . $subSelect . ')'),
                            'data_quitacao'     => new Zend_Db_Expr('(' . $dataQuitacao . ')')
                          )
                )
		->join(
                    array('t' => $dbTerceiro), 't.terceiro_id = l.terceiro_id',
                    array('t.terceiro_nome')
                )
                
                ->join(
                    array('df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id',
                    array('df.fn_doc_fiscal_numero')
	);
        

	if ( !empty( $this->_data['fn_conta_id'] ) )
	    $select->where( 'l.fn_conta_id = ?', $this->_data['fn_conta_id'] );

	if ( !empty( $this->_data['terceiro_id'] ) )
	    $select->where( 'l.terceiro_id = ?', $this->_data['terceiro_id'] );

	if ( !empty( $this->_data['fn_duplicata_tipo'] ) )
	    $select->where( 'd.fn_duplicata_tipo = ?', $this->_data['fn_duplicata_tipo'] );

	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_data ) >= ?', $this->_data['rel_data_ini'] );

	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_data ) <= ?', $this->_data['rel_data_fim'] );

        $select->order( array( 'l.fn_lancamento_data') );
        
	return $dbDuplicata->fetchAll( $select );
    }
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function Boleto()
    {
	$dbParcela         = App_Model_DbTable_Factory::get( 'Parcela' );
	$dbDuplicata       = App_Model_DbTable_Factory::get( 'Duplicata' );
        $dbLancamento      = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbTerceiro        = App_Model_DbTable_Factory::get( 'Terceiro' );
        $dbDocumentoFiscal = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );

	$select = $dbParcela->select()
		->setIntegrityCheck( false )
		->from(
                   array('p' => $dbParcela),
                   array(
                        'p.fn_duplicata_id',
                        'p.fn_parcela_ref',
                        'p.fn_parcela_vencimento',
                        'p.fn_parcela_valor'
                       )
		)
		->join(
                    array('d' => $dbDuplicata), 'd.fn_duplicata_id = p.fn_duplicata_id',
                    array(
                        'd.fn_duplicata_id',
                        'd.fn_duplicata_total',
                        'd.fn_duplicata_parcelas',
                        'd.fn_duplicata_tipo',
                        'd.fn_duplicata_quitada'
                       )
                )
		->join(
                    array('l' => $dbLancamento), 'l.fn_lancamento_id = p.fn_lancamento_id',
                    array(
                        'l.fn_lancamento_data',
                        'l.fn_lancamento_efetivado',
                        'l.fn_lancamento_valor',
                        'l.fn_lancamento_tipo',
                        'l.fn_lancamento_trans',
                        'l.fn_lancamento_estorno'
                    )
                )
		->join(
                    array('t' => $dbTerceiro), 't.terceiro_id = l.terceiro_id',
                    array('t.terceiro_nome')

                )
                ->join(
                    array('df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id',
                    array('df.fn_doc_fiscal_numero')
                );
        
	if ( !empty( $this->_data['fn_lancamento_efetivado'] ) ){
            
            $select->where( 'l.fn_lancamento_efetivado = ?', ( $this->_data['fn_lancamento_efetivado'] == 'S' ? 1 : 0 ) );
        }
	    
	if ( !empty( $this->_data['terceiro_id'] ) )
	    $select->where( 'l.terceiro_id = ?', $this->_data['terceiro_id'] );

	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_data ) >= ?', $this->_data['rel_data_ini'] );

	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( l.fn_lancamento_data ) <= ?', $this->_data['rel_data_fim'] );

        $select->order( array( 'l.fn_lancamento_data') );
        
	return $dbDuplicata->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function FaturaCartao()
    {
	$dbFatura           = App_Model_DbTable_Factory::get( 'Fatura' );
	$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
        $dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
        $dbCartaoCredito    = App_Model_DbTable_Factory::get( 'CartaoCredito' );

	$select = $dbFatura->select()
		->setIntegrityCheck( false )
		->from(
                   array( 'f' => $dbFatura ),
                   array(
                        'f.fn_cc_fat_id',
                        'f.fn_cc_id',
                        'f.fn_lancamento_id',
                        'f.fn_cc_fat_ref',
                        'f.fn_cc_fat_total',
                        'f.fn_cc_fat_vencimento',
                        'f.fn_cc_fat_efetivado'
                       )
		)
		->join(
                    array( 'cc' => $dbCartaoCredito ), 'cc.fn_cc_id = f.fn_cc_id',
                    array( 
                            'cc.fn_cc_descricao',
                            'cc.fn_cc_numero',
                            'cc.fn_cc_bandeira' 
                        )
                )
		->join(
                    array( 'lf' => $dbLancamentoFatura ), 'lf.fn_cc_fat_id = f.fn_cc_fat_id',
                    array( )
                )
		->join(
                    array( 'lc' => $dbLancamentoCartao ), 'lc.fn_lanc_cartao_id = lf.fn_lanc_cartao_id',
                    array(
                        'lc.fn_lanc_cartao_id',
                        'lc.fn_cc_id',
                        'lc.fn_doc_fiscal_id',
                        'lc.fn_lanc_cartao_desc',
                        'lc.fn_lanc_cartao_valor',
                        'lc.fn_lanc_cartao_data',
                        'lc.fn_lanc_cartao_status'
                    )
                );
        

	if ( !empty( $this->_data['fn_cc_id'] ) )
	    $select->where( 'f.fn_cc_id = ?', $this->_data['fn_cc_id'] );

	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( f.fn_cc_fat_vencimento ) >= ?', $this->_data['rel_data_ini'] );

	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( f.fn_cc_fat_vencimento ) <= ?', $this->_data['rel_data_fim'] );

	return $dbFatura->fetchAll( $select );
    }
    
    /**
     *
     * @param Zend_Db_Table_Rowset $data
     * @return array 
     */
    public function agrupaLancamentosFaturaCartao($data) 
    {
        
        $dataFinal = array(
            'total' => 0,
            'fat'   => array()
        );

        foreach ($data as $row) {
            
            if(empty($dataFinal['fat'][$row->fn_cc_fat_id])){
                
                $dataFinal['fat'][$row->fn_cc_fat_id] = array(
                                                            'data'  => $row,
                                                            'itens' => array( )
                                                        );
            }
            
            $dataFinal['fat'][$row->fn_cc_fat_id]['itens'][$row->fn_lanc_cartao_id] = $row;


            $dataFinal['total'] += (float) $row->fn_lanc_cartao_valor;
        }

        return $dataFinal;
    }
    
    /**
    *
    * @return Zend_Db_Table_Rowset
    */
    public function LancamentoCartao()
    {
	$dbFatura           = App_Model_DbTable_Factory::get( 'Fatura' );
	//$dbLancamentoFatura = App_Model_DbTable_Factory::get( 'LancamentoFatura' );
        $dbLancamentoCartao = App_Model_DbTable_Factory::get( 'LancamentoCartao' );
        $dbCartaoCredito    = App_Model_DbTable_Factory::get( 'CartaoCredito' );

    
	$select = $dbFatura->select()
		->setIntegrityCheck( false )
		->from(
                    array( 'lc' => $dbLancamentoCartao ), 
                    array(
                        'lc.fn_lanc_cartao_id',
                        'lc.fn_cc_id',
                        'lc.fn_doc_fiscal_id',
                        'lc.fn_lanc_cartao_desc',
                        'lc.fn_lanc_cartao_valor',
                        'lc.fn_lanc_cartao_data',
                        'lc.fn_lanc_cartao_status'
                    )
                )
		->join(
                    array( 'cc' => $dbCartaoCredito ), 'cc.fn_cc_id = lc.fn_cc_id',
                    array( 
                            'cc.fn_cc_descricao',
                            'cc.fn_cc_numero',
                            'cc.fn_cc_bandeira' 
                        )
                );
        

	if ( !empty( $this->_data['fn_cc_id'] ) )
	    $select->where( 'lc.fn_cc_id = ?', $this->_data['fn_cc_id'] );
	
	if ( !empty( $this->_data['fn_lanc_cartao_status'] ) )
	    $select->where( 'lc.fn_lanc_cartao_status = ?', $this->_data['fn_lanc_cartao_status'] );

	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( lc.fn_lanc_cartao_data ) >= ?', $this->_data['rel_data_ini'] );

	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( lc.fn_lanc_cartao_data ) <= ?', $this->_data['rel_data_fim'] );
		
	return $dbFatura->fetchAll( $select );
    }
    
    /**
    *
    * @return Zend_Db_Table_Rowset
    */
    public function ReconciliacaoBancaria()
    {
        
        $dbReconciliacao           = App_Model_DbTable_Factory::get( 'Reconciliacao' );
        $dbConta                   = App_Model_DbTable_Factory::get( 'Conta' );
        $dbReconciliacaoLancamento = App_Model_DbTable_Factory::get( 'ReconciliacaoLancamento' );
        $dbLancamento              = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbDocumentoFiscal         = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );

        
	$select = $dbReconciliacao->select()
		->setIntegrityCheck( false )
		->from(
			array('r' => $dbReconciliacao), 
                        array(
                            'r.fn_recon_id',
                            'r.fn_recon_ini_data', 
                            'r.fn_recon_ini_valor', 
                            'r.fn_recon_fim_data', 
                            'r.fn_recon_fim_valor', 
                            'r.fn_recon_efetivada', 
                            'r.fn_recon_dtefetivada'
                        )
		)
		->join(
                    array( 'c' => $dbConta ), 'c.fn_conta_id = r.fn_conta_id',
                    array(
                        'c.fn_conta_descricao',
                        'c.fn_conta_agencia'
                        )
        	)
		->join(
                    array( 'rl' => $dbReconciliacaoLancamento ), 'rl.fn_recon_id = r.fn_recon_id',
                    array( )
        	)
		->join(
                    array( 'l' => $dbLancamento ), 'l.fn_lancamento_id = rl.fn_lancamento_id',
                    array(
                        'l.fn_lancamento_id',
                        'l.fn_lancamento_tipo',
                        'l.fn_lancamento_data',
                        'l.fn_lancamento_dtefetivado',
                        'l.fn_lancamento_valor',
                        'l.fn_lancamento_trans',
                        )
        	)
		->joinleft(
                    array( 'df' => $dbDocumentoFiscal ), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id',
                    array(
                        'df.fn_doc_fiscal_numero'
                        )
        	);
        
        
    	if ( !empty( $this->_data['fn_conta_id'] ) )
	    $select->where( 'r.fn_conta_id = ?', $this->_data['fn_conta_id'] );

	if ( !empty( $this->_data['rel_data_ini'] ) && Zend_Date::isDate( $this->_data['rel_data_ini'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( r.fn_recon_ini_data ) >= ?', $this->_data['rel_data_ini'] );

	if ( !empty( $this->_data['rel_data_fim'] ) && Zend_Date::isDate( $this->_data['rel_data_fim'], 'yyyy-MM-dd' ) )
	    $select->where( 'DATE( r.fn_recon_ini_data ) <= ?', $this->_data['rel_data_fim'] );

        $select->order( array( 'l.fn_lancamento_data') );
        
	return $dbReconciliacao->fetchAll( $select );
    }
    
    /**
     *
     * @param Zend_Db_Table_Rowset $data
     * @return array 
     */
    public function agrupaLancamentosReconciliacaoBancaria($data) 
    {
        
        $dataFinal = array( 'total' => 0, 'reco' => array() );

        foreach ($data as $row) {

            if(empty($dataFinal['reco'][$row->fn_recon_id])){
                
                $dataFinal['reco'][$row->fn_recon_id] = array(
                                                            'data'  => $row,
                                                            'itens' => array( )
                                                        );
            }
            
            $dataFinal['reco'][$row->fn_recon_id]['itens'][$row->fn_lancamento_id] = $row;
            
	    if ( 'C' == $row->fn_lancamento_tipo )
		$dataFinal['total'] += (float)$row->fn_lancamento_valor;
	    else
		$dataFinal['total'] -= (float)$row->fn_lancamento_valor;
	    
        }

        return $dataFinal;
    }
}