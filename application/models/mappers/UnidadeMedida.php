<?php

/**
 * 
 * @version $Id: UnidadeMedida.php 297 2012-02-21 01:14:12Z helion $
 */
class Model_Mapper_UnidadeMedida extends App_Model_Mapper_Abstract
{
    /**
     * 
     */
    public function fetchGrid()
    {
        $dbUnidademedida = App_Model_DbTable_Factory::get('UnidadeMedida');

        $select = $dbUnidademedida->select()
                ->setIntegrityCheck(false);
        

        $rows = $dbUnidademedida->fetchAll( $select );

        $data = array('rows' => array());

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->unidade_medida_id,
                    'data'  => array(
                        ++$key,
                        $row->unidade_medida_nome,
                        parent::_showStatus( $row->unidade_medida_status )
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

            $dbTable = App_Model_DbTable_Factory::get('UnidadeMedida');

            $where = array( 'UPPER(unidade_medida_nome) = UPPER(?)' => $this->_data['unidade_medida_nome'] );

            if ( !$dbTable->isUnique( $where, $this->_data['unidade_medida_id'] ) ) {

                $this->_message->addMessage( 'Unidade de medida j&aacute; cadastrada com esse nome .', App_Message::ERROR );
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
        $where = array( 'unidade_medida_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'UnidadeMedida' ), $where );
    }
}