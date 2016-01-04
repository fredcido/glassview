<?php

/**
 * 
 * @version $Id: Recibo.php 816 2012-09-30 14:42:20Z fred $
 */
class Model_Mapper_Recibo extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return array 
     */
    public function fetchGrid()
    {
        $rows = $this->listAll();
	
        $data = array( 'rows' => array() );

        if ( $rows->count() ) {
	    
	    $currency = new Zend_Currency();
	    $date = new Zend_Date();

            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->fn_recibo_id,
                    'data'  => array(
                        ++$key,
			0,
			str_pad( $row->fn_recibo_id, 11, '0', STR_PAD_LEFT ),
                        $row->nome,
                        $row->cpf_cnpj,
                        $currency->setValue( $row->fn_recibo_valor )->toString(),
                        $date->set( $row->fn_recibo_data )->toString('d/MM/Y')
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Recibo' );
	    
	    if ( preg_match( '/^T/i', $this->_data['receptor'] ) )
		$this->_data['terceiro_id'] = preg_replace( '/[^0-9]/', '', $this->_data['receptor'] );
	    else
		$this->_data['funcionario_id'] = preg_replace( '/[^0-9]/', '', $this->_data['receptor'] );
	    
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
        $where = array( 'fn_recibo_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Recibo' ), $where );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset 
     */
    public function listAll( array $recibos = array() )
    {
	$dbRecibo = App_Model_DbTable_Factory::get( 'Recibo' );
	$dbTerceiro = App_Model_DbTable_Factory::get( 'Terceiro' );
	$dbFuncionario = App_Model_DbTable_Factory::get( 'Funcionario' );
	$dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	
	$selectTerceiros = $dbRecibo->select()
				    ->setIntegrityCheck( false )
				    ->from( array( 'r' => $dbRecibo ) )
				    ->join(
					    array( 't' => $dbTerceiro ),
					    't.terceiro_id = r.terceiro_id',
					    array( 'cpf_cnpj' => 'terceiro_cpf_cnpj', 'nome' => 'terceiro_nome' )
				    );
	
	if ( !empty( $recibos ) )
	    $selectTerceiros->where( 'fn_recibo_id IN (?)', $recibos );
	
	$selectFuncionario = $dbRecibo->select()
				      ->setIntegrityCheck( false )
				      ->from( array( 'r' => $dbRecibo ) )
				      ->join(
					    array( 'f' => $dbFuncionario ),
					    'f.funcionario_id = r.funcionario_id',
					    array( 'cpf_cnpj' => 'funcionario_cpf_cnpj' )
				      )
				      ->joinLeft(
					    array( 'u' => $dbUsuario ),
					    'u.usuario_id = f.usuario_id',
					    array()
					)
					->joinLeft(
					    array( 'df' => $dbDadosFuncionario ),
					    'df.funcionario_id = f.funcionario_id',
					    array(
						'nome' => new Zend_Db_Expr( 'IFNULL( u.usuario_nome, df.dados_func_nome )' )
					    )
					);
	
	if ( !empty( $recibos ) )
	    $selectFuncionario->where( 'fn_recibo_id IN (?)', $recibos );
	
	$select = $dbRecibo->select()
			    ->union( array( $selectTerceiros, $selectFuncionario ) )
			    ->order( 'fn_recibo_data DESC' );
	
	return $dbRecibo->fetchAll( $select );
    }
}