<?php

/**
 * 
 * @version $Id: Preferencia.php 410 2012-03-07 16:15:50Z fred $
 */
class Model_Mapper_Preferencia extends App_Model_Mapper_Abstract
{
    
    /**
     *
     * @return boolean 
     */
    public function save()
    {
        try {
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Usuario' );
	    
	    if ( empty( $this->_data['usuario_senha'] ) )
		unset( $this->_data['usuario_senha'] );
	    else
		$this->_data['usuario_senha'] = sha1( $this->_data['usuario_senha'] );
	    
            parent::_cleanCacheTag( array( 'translate' ) );
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
        $where = array( 'usuario_id = ?' => $this->_data['id'] );
        
        $usuario = parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Usuario' ), $where );
	$usuario->usuario_senha =  null;
	
	return $usuario;
    }
}