<?php

/**
 * 
 * @version $Id: TipoAtivo.php 231 2012-02-14 18:10:58Z fred $
 */
class Model_Mapper_TipoAtivo extends App_Model_Mapper_Abstract
{
    public function fetchGrid()
    {
        $dbTipoAtivo = App_Model_DbTable_Factory::get( 'TipoAtivo' );
        
        $rows = $dbTipoAtivo->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->tipo_ativo_id,
                    'data'  => array(
                        ++$key,
                        $row->tipo_ativo_nome,
                        $row->tipo_ativo_descricao,
			parent::_showStatus( $row->tipo_ativo_status )
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'TipoAtivo' );
	    
	    $where = array( 'UPPER(tipo_ativo_nome) = UPPER(?)' => $this->_data['tipo_ativo_nome'] );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['tipo_ativo_id'] ) ) {

		$this->_message->addMessage( 'Tipo de ativo j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		return false;
	    }
	    
	    return parent::_simpleSave( App_Model_DbTable_Factory::get( 'TipoAtivo' ) );

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
        $where = array( 'tipo_ativo_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'TipoAtivo' ), $where );
    }
}