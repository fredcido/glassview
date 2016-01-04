<?php

/**
 * 
 * @version $Id: Reconciliacao.php 1027 2013-10-18 18:58:59Z helion $
 */
class Model_Mapper_Reconciliacao extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        
        
        $dbReconciliacao = App_Model_DbTable_Factory::get( 'Reconciliacao' );
        $dbConta         = App_Model_DbTable_Factory::get( 'Conta' );

        
	$select = $dbReconciliacao->select()
		->setIntegrityCheck( false )
		->from(
			array('r' => $dbReconciliacao), array('r.*')
		)
		->join(
                    array('c' => $dbConta), 'c.fn_conta_id = r.fn_conta_id',
                    array('c.*')
        	);
        
        
        $rows = $dbReconciliacao->fetchAll($select);
        
        $data = array('rows' => array());
        
        

        $date = new Zend_Date();
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $currency = new Zend_Currency();
                
                $dataEfetivacao = (empty($row->fn_recon_dtefetivada) ? '-' : $date->set( $row->fn_recon_dtefetivada )->toString( 'dd/MM/yyyy' ) );
                
                $data['rows'][] = array(
                    'id'    => $row->fn_recon_id,
                    'data'  => array(
                        ++$key,
                        $row->fn_conta_descricao,
                        $date->set( $row->fn_recon_ini_data )->toString( 'dd/MM/yyyy' ),
                        $currency->setValue( (float)$row->fn_recon_ini_valor )->toString(),
                        $date->set( $row->fn_recon_fim_data )->toString( 'dd/MM/yyyy' ),
                        $currency->setValue( (float)$row->fn_recon_fim_valor )->toString(),
                        $dataEfetivacao,
                        $this->_getEfetivacao($row->fn_recon_efetivada)
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
    protected function _getEfetivacao( $type )
    {
	$optEfetivacao = array(  
                                0 => 'Pendente' , 
                                1 => 'Efetivado'
                          );

	return ( empty( $optEfetivacao[$type] ) ? '-' : $optEfetivacao[$type] );
    }
    /**
     *
     * @return boolean 
     */
    public function save()
    {
        
        $adapter = Zend_Db_Table::getDefaultAdapter();
        
        try {
            
		$dbReconciliacao = App_Model_DbTable_Factory::get('Reconciliacao');
                
                $adapter->beginTransaction();


                if( $this->_data['fn_recon_efetivada'] == 1){

                    $this->_data['fn_recon_dtefetivada'] = Zend_Date::now()->toString('yyyy-MM-dd');
                }
                
                $result  = parent::_simpleSave( $dbReconciliacao );

                $lancamentosInsert = array();
                $lancamentosDelete = array();
                $lancamentos = $this->trataIdLancamentos( $this->_data['lancamentos'] );
                if(empty($this->_data['fn_recon_id'])){
                    
                    $this->_data['fn_recon_id'] = $result;
                    $lancamentosInsert = $lancamentos;
                }else{
                    
                    $lancsBD = $this->buscaLancamentosDeReconciliacao($this->_data['fn_recon_id']);

                    $lancamentosInBD = array();
                    foreach ($lancsBD as $row) {
                        
                        $lancamentosInBD[] = $row->fn_lancamento_id;
                    }

                    $lancamentosDelete = array_diff( $lancamentosInBD, $lancamentos );
                    $lancamentosInsert = array_diff( $lancamentos , $lancamentosInBD );
                }
                
                $dbReconciliacaoLancamento = App_Model_DbTable_Factory::get('ReconciliacaoLancamento');
                
                foreach ( $lancamentosDelete as $lancamento ) {

                    $dbReconciliacaoLancamento->delete(
                            array(
                                    'fn_recon_id = ?'      => $this->_data['fn_recon_id'],
                                    'fn_lancamento_id = ?' => $lancamento,
                                )
                            );
                }

                foreach ( $lancamentosInsert as $lancamento ) {

                    $dbReconciliacaoLancamento->insert(
                            array(
                                    'fn_recon_id'      => $this->_data['fn_recon_id'],
                                    'fn_lancamento_id' => $lancamento,
                                )
                            );
                }
                $adapter->commit();

                return $result;
            
	} catch ( Exception $e ) {

	    $adapter->rollBack();
	    
	    return false;
	}
    }
    
    private function trataIdLancamentos( $param )
    {
        $ids = array();
        
        if ( !empty( $param ) ) {

            foreach ($param as $value) {

                if (!empty($value)){
                    
                    $ids[] = $value;
                }
            }
        }
        
        return $ids;
    }
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'fn_recon_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Reconciliacao' ), $where );
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function buscaLancamentosDeReconciliacao( $reconId )
    {
	$dbReconciliacaoLancamento = App_Model_DbTable_Factory::get('ReconciliacaoLancamento');

	$select = $dbReconciliacaoLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('rl' => $dbReconciliacaoLancamento), array('rl.*')
		)
                ->where( 'rl.fn_recon_id = ?' , $reconId );


	return $dbReconciliacaoLancamento->fetchAll( $select );
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function buscaLancamentosForaDeConciliacao( $data , $datainicio )
    {
        
        $dbLancamento              = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbReconciliacaoLancamento = App_Model_DbTable_Factory::get( 'ReconciliacaoLancamento' );
        $dbDocumentoFiscal         = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        
        
        $subSelect = $dbReconciliacaoLancamento->select()
                ->from(
			array('r' => $dbReconciliacaoLancamento), array('r.fn_lancamento_id')
		)
                ->where( 'r.fn_lancamento_id = l.fn_lancamento_id' );
        
	$select = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('l' => $dbLancamento), array('l.*')
		)
		->joinleft(
                    array('df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id',
                    array('df.fn_doc_fiscal_numero')
        	)
                ->where( 'l.fn_conta_id = ?' , $data['conta'] )
                ->where( "l.fn_lancamento_dtefetivado BETWEEN '".$datainicio."' AND '".$data['datafim']."'" )
                ->where( 'l.fn_lancamento_id NOT IN(?)', $subSelect )
                ->order( array( 'l.fn_lancamento_dtefetivado DESC') );

	$rows = $dbLancamento->fetchAll( $select );

        $date = new Zend_Date();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as  $row ) {
                
                $currency = new Zend_Currency();
                
                if($row->fn_lancamento_tipo == 'C'){
                    
                    $lancValor = $row->fn_lancamento_valor;
                    $font = '<font color="blue">';
                }else{
                    
                    $lancValor = '-'.$row->fn_lancamento_valor;
                    $font = '<font color="red">';
                }
                
                $date->set( $row->fn_lancamento_dtefetivado );
                    
                $data['rows'][] = array(
                    'id'    => $row->fn_lancamento_id,
                    'style' => array('color' => 'red'),
                    'data'  => array(
                        $font.(empty($row->fn_lancamento_trans) ? $row->fn_doc_fiscal_numero : 'Transfer&ecirc;ncia').'</font>',
                        $font.$date->toString( 'dd/MM/yyyy' ).'</font>',
			$font.$currency->setValue( (float)$lancValor )->toString().'</font>',
                    )
                );
                
            }
            
        }
        
        return $data;
    } 
    /**
     *
     * @param array $data
     * @return array
     */
    public function buscaLancamentosEmConciliacao( $data , $datainicio )
    {

        $dbLancamento              = App_Model_DbTable_Factory::get( 'Lancamento' );
        $dbReconciliacaoLancamento = App_Model_DbTable_Factory::get( 'ReconciliacaoLancamento' );
        $dbDocumentoFiscal         = App_Model_DbTable_Factory::get( 'DocumentoFiscal' );
        $dbConta                   = App_Model_DbTable_Factory::get( 'Conta' );
	
        $subContaOrig = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('la' => $dbLancamento),
                        array('')
		)
		->join(
                    array('c' => $dbConta), 'c.fn_conta_id = la.fn_conta_id',
                    array('c.fn_conta_descricao')
        	)
                ->where( 'la.fn_lancamento_id = l.fn_lancamento_anterior' );

        $subContaDest = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('ld' => $dbLancamento),
                        array('')
		)
		->join(
                    array('cd' => $dbConta), 'cd.fn_conta_id = ld.fn_conta_id',
                    array('cd.fn_conta_descricao')
        	)
                ->where( 'ld.fn_lancamento_anterior = l.fn_lancamento_id' );
        
        $subSelect = $dbReconciliacaoLancamento->select()
                ->from(
			array('r' => $dbReconciliacaoLancamento), array('r.fn_lancamento_id')
		)
                ->where( 'r.fn_lancamento_id = l.fn_lancamento_id' )
                ->where( 'r.fn_recon_id = ?' , $data['recon'] );

	$select = $dbLancamento->select()
		->setIntegrityCheck( false )
		->from(
			array('l' => $dbLancamento),
                        array(
                                'l.*',
                                'conta_origem' => new Zend_Db_Expr( '(' . $subContaOrig . ')'),
                                'conta_destino' => new Zend_Db_Expr( '(' . $subContaDest . ')')
                                        )
                        //array('l.*')
		)
                
		->joinleft(
                    array('df' => $dbDocumentoFiscal), 'df.fn_doc_fiscal_id = l.fn_doc_fiscal_id',
                    array('df.fn_doc_fiscal_numero')
        	)
		->join(
                    array('c' => $dbConta), 'c.fn_conta_id = l.fn_conta_id',
                    array('c.fn_conta_descricao')
        	)
                ->where( "l.fn_lancamento_dtefetivado BETWEEN '".$datainicio."' AND '".$data['datafim']."'" )
                ->where( 'l.fn_lancamento_id IN(?)', $subSelect )
                ->order( array( 'l.fn_lancamento_dtefetivado DESC') );
	$rows = $dbLancamento->fetchAll( $select );

        $date = new Zend_Date();

        $data = array('rows' => array());

        if ( $rows->count() ) {

            foreach ( $rows as  $row ) {

                $currency = new Zend_Currency();

                if($row->fn_lancamento_tipo == 'C'){

                    $lancValor = $row->fn_lancamento_valor;
                    $font = '<font color="blue">';
                    if(empty($row->fn_lancamento_trans)){
                        
                        $descricao = $row->fn_doc_fiscal_numero;
                    }else{
                       
                        $descricao = 'Transf ('.$row->conta_origem.'/'.$row->fn_conta_descricao.')';
                        $descricao = 'Transfer&ecirc;ncia';
                    }
                }else{

                    $lancValor = '-'.$row->fn_lancamento_valor;
                    $font = '<font color="red">';
                    if(empty($row->fn_lancamento_trans)){
                        
                        $descricao = $row->fn_doc_fiscal_numero;
                    }else{
                       
                        $descricao = 'Transf ('.$row->fn_conta_descricao.'/'.$row->conta_destino.')';
                        $descricao = 'Transfer&ecirc;ncia';
                    }
                }

                $date->set( $row->fn_lancamento_dtefetivado );

                $data['rows'][] = array(
                    'id'    => $row->fn_lancamento_id,
                    'style' => array('color' => 'red'),
                    'data'  => array(
                        $font.$descricao.'</font>',
                        $font.$date->toString( 'dd/MM/yyyy' ).'</font>',
			$font.$currency->setValue( (float)$lancValor )->toString().'</font>'
                    )
                );

            }

        }

        return $data;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function buscaDadosStartReconcilicao( $data )
    {
        
        $dbReconciliacao   = App_Model_DbTable_Factory::get( 'Reconciliacao' );
        
	$subSelect = $dbReconciliacao->select()
		->from(
			array('rs' => $dbReconciliacao), array('fn_recon_id' => 'MAX(rs.fn_recon_id)')
		)
                ->where( 'rs.fn_conta_id = ?' , $data['conta'] )
                ->where( 'rs.fn_recon_efetivada = ?' , 1 );
        
	$select = $dbReconciliacao->select()
		->from(
			array('r' => $dbReconciliacao), array('r.*')
		)
                ->where( 'r.fn_recon_id IN(?)' , $subSelect );
        
	$rows = $dbReconciliacao->fetchAll( $select );

        if ( $rows->count() != 0 ) {
            
            foreach ( $rows as  $row ) {
               
                $data = array(
                    'data_ini'  => $row->fn_recon_fim_data,
                    'valor_ini' => (float)$row->fn_recon_fim_valor
                    
                );
                
            }
            
        }else{
            
            $row  = parent::_simpleFetchRow( 
                                App_Model_DbTable_Factory::get( 'Conta' ),
                                array( 'fn_conta_id = ?' => $data['conta'] )
                    );
            
            $data = array(
                'data_ini'  => $row->fn_conta_data_cad,
                'valor_ini' => (float)$row->fn_conta_saldo_inicial

            );
            
        }
        
        return $data;
    } 
    
    /**
     *
     * @param array $data
     * @return bool 
     */
    public function isEfetivado( $fn_recon_id )
    {
        $dbReconciliacao = App_Model_DbTable_Factory::get( 'Reconciliacao' );
	$select = $dbReconciliacao->select()
		->from(
			array('r' => $dbReconciliacao), array('r.*')
		)
                ->where( 'r.fn_recon_efetivada = ?' , 1 )
                ->where( 'r.fn_recon_id = ?' , $fn_recon_id );
        
	$rows = $dbReconciliacao->fetchAll( $select );

        if ( $rows->count() != 0 ) {
            
            return true;
        }else{
            
            return false;
        }
        
    } 
    /**
     *
     * @param array $data
     * @return array 
     */
    public function buscaDadosLancamento( $lanc_id )
    {
        return parent::_simpleFetchRow( 
                            App_Model_DbTable_Factory::get( 'Lancamento' ),
                            array( 'fn_lancamento_id = ?' => $lanc_id )
                );
    }
}