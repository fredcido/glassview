<?php

/**
 * 
 * @version $Id: Modulo.php 231 2012-02-14 18:10:58Z fred $
 */
class Model_Mapper_Modulo extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        $dbModulo = App_Model_DbTable_Factory::get('Modulo');
        
        $rows = $dbModulo->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->modulo_id,
                    'data'  => array(
                        ++$key,
                        $row->modulo_nome,
                        ( $row->modulo_status ? 'Liberado' : 'Bloqueado' )
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
            
		$dbTable = App_Model_DbTable_Factory::get('Modulo');
		
		$where = array( 'UPPER(modulo_nome) = UPPER(?)' => $this->_data['modulo_nome'] );
		
		if ( !$dbTable->isUnique( $where, $this->_data['modulo_id'] ) ) {
		    
		    $this->_message->addMessage( 'M&oacute;dulo j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		    return false;
		}
	    
        	if ( false !== ($result = parent::_simpleSave( $dbTable ) ) )
        		parent::_cleanCacheTag( array('acl') );
        	
        	return $result;
            
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
        $where = array( 'modulo_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Modulo' ), $where );
    }
}