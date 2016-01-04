<?php

/**
 * 
 * @version $Id: Perfil.php 231 2012-02-14 18:10:58Z fred $
 */
class Model_Mapper_Perfil extends App_Model_Mapper_Abstract
{
    public function fetchGrid()
    {
        $dbPerfil = App_Model_DbTable_Factory::get('Perfil');
        
        $rows = $dbPerfil->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->perfil_id,
                    'data'  => array(
                        ++$key,
                        $row->perfil_nome,
                        $row->perfil_descricao,
                        parent::_showStatus($row->perfil_status)
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Perfil' );
	    
	    $where = array( 'UPPER(perfil_nome) = UPPER(?)' => $this->_data['perfil_nome'] );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['perfil_id'] ) ) {

		$this->_message->addMessage( 'Perfil j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		return false;
	    }
	    
	    return parent::_simpleSave( App_Model_DbTable_Factory::get('Perfil'));

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
        $where = array( 'perfil_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Perfil' ), $where );
    }
}