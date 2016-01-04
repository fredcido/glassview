<?php

/**
 * 
 * @version $Id: LinguagemTermo.php 330 2012-02-23 12:42:19Z helion $
 */
class Model_Mapper_LinguagemTermo extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        $dbLinguagemTermo = App_Model_DbTable_Factory::get('LinguagemTermo');
        
        $rows = $dbLinguagemTermo->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->linguagem_termo_id,
                    'data'  => array(
                        ++$key,
                        $row->linguagem_termo_desc
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
            
		$dbTable = App_Model_DbTable_Factory::get('LinguagemTermo');
		
		$where = array( 'UPPER(linguagem_termo_desc) = UPPER(?)'  => $this->_data['linguagem_termo_desc'] );
		
		if ( !$dbTable->isUnique( $where, $this->_data['linguagem_termo_id'] ) ) {
		    
		    $this->_message->addMessage( 'Termo j&aacute; cadastrado.', App_Message::ERROR );
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
        $where = array( 'linguagem_termo_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'LinguagemTermo' ), $where );
    }
}