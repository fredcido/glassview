<?php

/**
 * 
 * @version $Id: LembreteConfig.php 489 2012-04-04 17:53:29Z fred $
 */
class Model_Mapper_LembreteConfig extends App_Model_Mapper_Abstract
{
   
    /**
     *
     * @return boolean
     */
    public function save()
    {
	$dbLembreteConfig = App_Model_DbTable_Factory::get( 'LembreteConfig' );
	$dbLembreteConfig->getAdapter()->beginTransaction();
        try {
	    
	    $whereDel = array( 'lembrete_config_tipo = ?' => $this->_data['lembrete_config_tipo'] );
	    $dbLembreteConfig->delete( $whereDel );
	    
	    foreach ( $this->_data['perfis'] as $perfil ) {
		
		$row = $dbLembreteConfig->createRow();
		$row->perfil_id = $perfil;
		$row->lembrete_config_tipo = $this->_data['lembrete_config_tipo'];
		$row->save();
	    }
	    
	    $dbLembreteConfig->getAdapter()->commit();
	    
	    return true;

        } catch ( Exception $e ) {
	    
	    $dbLembreteConfig->getAdapter()->rollBack();
            return false;
        }
    }
    
    /**
     *
     * @param string $tipo
     * @return array
     */
    public function listPerfis( $tipo )
    {
	$dbLembreteConfig = App_Model_DbTable_Factory::get( 'LembreteConfig' );
	$rows = $dbLembreteConfig->fetchAll( array( 'lembrete_config_tipo = ?' => $tipo ) );
	
	$data = array();
	foreach ( $rows as $row )
	    $data[] = (int)$row->perfil_id;
	
	return $data;
    }
}