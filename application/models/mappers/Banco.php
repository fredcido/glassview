<?php

/**
 * 
 * @version $Id: Banco.php 499 2012-04-14 12:23:13Z helion $
 */
class Model_Mapper_Banco extends App_Model_Mapper_Abstract
{
    
    public function fetchGrid()
    {

	$dbBanco = App_Model_DbTable_Factory::get( 'Banco' );

        $rows = $dbBanco->fetchAll();

        $data = array( 'rows' => array() );

	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_banco_id,
		    'data' => array(
			++$key,
			$row->fn_banco_codigo,
			$row->fn_banco_nome
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Banco' );
	    
	    $where = array( 'UPPER(fn_banco_codigo) = UPPER(?)' => $this->_data['fn_banco_codigo']);
		
	    if ( !$dbTable->isUnique( $where, $this->_data['fn_banco_id'] ) ) {

                $translate = Zend_Registry::get('Zend_Translate');
                
		$this->_message->addMessage( $translate->_('Conta já cadastrada.'), App_Message::ERROR );
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
        $where = array( 'fn_banco_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Banco' ), $where );
    }
    
}