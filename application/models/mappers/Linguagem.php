<?php

/**
 * 
 * @version $Id: Linguagem.php 330 2012-02-23 12:42:19Z helion $
 */
class Model_Mapper_Linguagem extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        $dbLinguagem = App_Model_DbTable_Factory::get('Linguagem');
        
        $rows = $dbLinguagem->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->linguagem_id,
                    'data'  => array(
                        ++$key,
                        $row->linguagem_nome,
                        $row->linguagem_local,
                        parent::_showStatus($row->linguagem_status)
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
            
		$dbTable = App_Model_DbTable_Factory::get('Linguagem');
		
		$where = array( 'UPPER(linguagem_nome) = UPPER(?)'  => $this->_data['linguagem_nome'],
                                'UPPER(linguagem_local) = UPPER(?)' => $this->_data['linguagem_local'] );
		
		if ( !$dbTable->isUnique( $where, $this->_data['linguagem_id'] ) ) {
		    
		    $this->_message->addMessage( 'Linguagem j&aacute; cadastrada.', App_Message::ERROR );
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
        $where = array( 'linguagem_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Linguagem' ), $where );
    }
}