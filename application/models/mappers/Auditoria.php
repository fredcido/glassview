<?php

/**
 * 
 * @version $Id: Auditoria.php 820 2012-10-08 20:32:10Z ze $
 */
class Model_Mapper_Auditoria extends App_Model_Mapper_Abstract
{   
    /**
     *
     * @return array 
     */
    public function fetchGrid()
    {
        $dbAuditoria = App_Model_DbTable_Factory::get('Auditoria');
        $dbUsuario   = App_Model_DbTable_Factory::get('Usuario');
        
        $select = $dbAuditoria->select()
                ->setIntegrityCheck(false)
                ->from(
		    array( 'au' => $dbAuditoria )
                )
                ->join(
		    array( 'u' => $dbUsuario ),
		    'au.usuario_id = u.usuario_id',
		    array( 'u.usuario_nome' )
                )
		->order( 'auditoria_data DESC' )
		->limit( 1000 );
        
        $rows = $dbAuditoria->fetchAll( $select );
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
	    
	    $date = new Zend_Date();
	    
            foreach ( $rows as $key => $row ) {
                
		$date->set( $row->auditoria_data );
		
		$sql = strlen( $row->auditoria_sql ) > 70 ? 
		       substr( $row->auditoria_sql, 0, 70 ) . '...' : 
		       $row->auditoria_sql;
		
                $data['rows'][] = array(
                    'id'    => $row->auditoria_id,
                    'data'  => array(
                        ++$key,
			$sql,
                        $date->toString( 'dd/MM/yyyy HH:mm:ss' ),
                        $row->auditoria_path,
                        $row->usuario_nome,
                        $row->auditoria_ip
                    )
                );   
            }
            
        }
        
        return $data;
    }
    
    /**
     *
     * @param Zend_Db_Profiler_Query $query
     * @return boolean 
     */
    public function save( $query )
    {

	//return true;
	try {
	    
	    $request = Zend_Controller_Front::getInstance()->getRequest();
	    
	    $dbAuditoria = App_Model_DbTable_Factory::get( 'Auditoria' );
	    
	    $adapter = $dbAuditoria->getAdapter();
	    
	    //$newAdapter = Zend_Db::factory( 'Pdo_Mysql', $adapter->getConfig() );
	    //$dbAuditoria->setDefaultAdapter( $newAdapter );
	    
	    $dbAuditoria->insert(
		array(
		    'auditoria_id'     => $this->randomId(),
		    'usuario_id'       => Zend_Auth::getInstance()->getIdentity()->usuario_id,
		    'auditoria_path'   => $request->getPathInfo(),
		    'auditoria_ip'     => $_SERVER['REMOTE_ADDR'],
		    'auditoria_sql'    => $query->getQuery(),
		    'auditoria_params' => print_r( $query->getQueryParams(), true )
		)
	    );
	    
	    return true;
	    
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
	$dbAuditoria = App_Model_DbTable_Factory::get( 'Auditoria' );
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	
	$select = $dbAuditoria->select()
			      ->setIntegrityCheck( false )
			      ->from( array( 'a' => $dbAuditoria ) )
			      ->join( 
				  array( 'u' => $dbUsuario ), 
				  'u.usuario_id = a.usuario_id',
				  array( 'usuario' => 'usuario_nome' )
			      )
			      ->where( 'a.auditoria_id = ?', $this->_data['id'] );
	
	return $dbAuditoria->fetchRow( $select );
    }
}
