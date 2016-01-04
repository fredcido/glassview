<?php

/**
 * 
 * @version $Id: CartaoCredito.php 518 2012-04-27 17:38:19Z helion $
 */
class Model_Mapper_CartaoCredito extends App_Model_Mapper_Abstract
{
    
    public function fetchGrid()
    {

	$dbCartaoCredito = App_Model_DbTable_Factory::get( 'CartaoCredito' );

        $rows = $dbCartaoCredito->fetchAll();

        $data = array( 'rows' => array() );
        
        $date = new Zend_Date();
        
	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {
                
                if( empty( $row->fn_cc_validade )){
                    
                    $ccValidade = '-';
                }else{
                    $date->set( $row->fn_cc_validade );
                    
                    $ccValidade = $date->toString( 'MM/yyyy' );
                }
                
		$data['rows'][] = array(
		    'id' => $row->fn_cc_id,
		    'data' => array(
			++$key,
			$row->fn_cc_descricao,
			$row->fn_cc_titular,
			$row->fn_cc_numero,
			$ccValidade,
			$this->_getStatusCartaoCreditos($row->fn_cc_status)
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'CartaoCredito' );
	    
	    $where = array( 'UPPER(fn_cc_numero) = UPPER(?)' => $this->_data['fn_cc_numero'] );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['fn_cc_id'] ) ) {

                $translate = Zend_Registry::get('Zend_Translate');
                
		$this->_message->addMessage( $translate->_('Número de cartão já cadastrado.'), App_Message::ERROR );
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
        $where = array( 'fn_cc_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'CartaoCredito' ), $where );
    }
    
    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getStatusCartaoCreditos( $type )
    {
	$optStatus = array(
                            'A'  => 'Ativo',
                            'B'  => 'Bloqueado',
                            'F'  => 'Bloqueado por fraude',
                            'C'  => 'Cancelado'
                          );

	return ( empty( $optStatus[$type] ) ? '-' : $optStatus[$type] );
    }
    
}