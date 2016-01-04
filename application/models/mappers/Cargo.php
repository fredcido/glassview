<?php

/**
 * 
 * @version $Id: Cargo.php 260 2012-02-15 17:58:40Z fred $
 */
class Model_Mapper_Cargo extends App_Model_Mapper_Abstract
{
    public function fetchGrid()
    {
        $dbCargo = App_Model_DbTable_Factory::get('Cargo');

        $rows = $dbCargo->fetchAll();

        $data = array( 'rows' => array() );

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->cargo_id,
                    'data'  => array(
                        ++$key,
                        $row->cargo_nome,
                        $row->cargo_descricao,
                        parent::_showStatus($row->cargo_status)
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Cargo' );
	    
	    $where = array( 'UPPER(cargo_nome) = UPPER(?)' => $this->_data['cargo_nome'] );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['cargo_id'] ) ) {

		$this->_message->addMessage( 'Cargo j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		return false;
	    }
	    
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
        $where = array( 'cargo_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Cargo' ), $where );
    }
}