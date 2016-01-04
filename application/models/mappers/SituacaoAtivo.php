<?php

/**
 * 
 * @version $Id: SituacaoAtivo.php 231 2012-02-14 18:10:58Z fred $
 */
class Model_Mapper_SituacaoAtivo extends App_Model_Mapper_Abstract
{
    public function fetchGrid()
    {
        $dbSituacaoAtivo = App_Model_DbTable_Factory::get( 'SituacaoAtivo' );
        
        $rows = $dbSituacaoAtivo->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->situacao_ativo_id,
                    'data'  => array(
                        ++$key,
                        $row->situacao_ativo_nome,
                        $row->situacao_ativo_descricao
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'SituacaoAtivo' );
	    
	    $where = array( 'UPPER(situacao_ativo_nome) = UPPER(?)' => $this->_data['situacao_ativo_nome'] );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['situacao_ativo_id'] ) ) {

		$this->_message->addMessage( 'Situacao de ativo j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		return false;
	    }
	    
	    return parent::_simpleSave( App_Model_DbTable_Factory::get( 'SituacaoAtivo' ) );

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
        $where = array( 'situacao_ativo_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'SituacaoAtivo' ), $where );
    }
}