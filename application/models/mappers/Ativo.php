<?php

/**
 * 
 * @version $Id: Ativo.php 231 2012-02-14 18:10:58Z fred $
 */
class Model_Mapper_Ativo extends App_Model_Mapper_Abstract
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
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->ativo_id,
                    'data'  => array(
                        ++$key,
                        $row->ativo_nome,
                        $row->filial,
                        $row->ativo_patrimonio,
                        $row->tipo_ativo_nome,
                        $row->situacao_ativo_nome,
			parent::_showStatus( $row->ativo_status )
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Ativo' );
	    
	    $where = array( 'UPPER(ativo_nome) = UPPER(?)' => $this->_data['ativo_nome'] );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['ativo_id'] ) ) {

		$this->_message->addMessage( 'Ativo j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		return false;
	    }
	    
	    return parent::_simpleSave( App_Model_DbTable_Factory::get( 'Ativo' ) );

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
        $where = array( 'ativo_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Ativo' ), $where );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listAll()
    {
	$dbAtivo = App_Model_DbTable_Factory::get( 'Ativo' );
	$dbTipoAtivo = App_Model_DbTable_Factory::get( 'TipoAtivo' );
	$dbSituacaoAtivo = App_Model_DbTable_Factory::get( 'SituacaoAtivo' );
	$dbFilial = App_Model_DbTable_Factory::get( 'Filial' );
	
	$select = $dbAtivo->select()
			  ->setIntegrityCheck( false )
			  ->from( array( 'a' => $dbAtivo ) )
			  ->join(
			    array( 'ta' => $dbTipoAtivo ),
			    'ta.tipo_ativo_id = a.tipo_ativo_id',
			    array( 'tipo_ativo_nome' )
			  )
			  ->join(
			    array( 'f' => $dbFilial ),
			    'f.filial_id = a.filial_id',
			    array( 'filial' => 'filial_nome' )
			  )
			  ->join(
			    array( 'sa' => $dbSituacaoAtivo ),
			    'sa.situacao_ativo_id = a.situacao_ativo_id',
			    array( 'situacao_ativo_nome' )
			  )
			  ->order( 'ativo_nome' );
	
	return $dbAtivo->fetchAll( $select );
    }
}