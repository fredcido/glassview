<?php

/**
 * 
 * @version $Id: TipoProduto.php 298 2012-02-21 01:43:06Z helion $
 */
class Model_Mapper_TipoProduto extends App_Model_Mapper_Abstract
{
    /**
     * 
     */
    public function fetchGrid()
    {
        $dbTipoProduto = App_Model_DbTable_Factory::get('TipoProduto');

        $select = $dbTipoProduto->select()
                ->setIntegrityCheck(false);
        

        $rows = $dbTipoProduto->fetchAll( $select );

        $data = array('rows' => array());

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->tipo_produto_id,
                    'data'  => array(
                        ++$key,
                        $row->tipo_produto_nome,
                        $row->tipo_produto_descricao
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

            $dbTable = App_Model_DbTable_Factory::get('TipoProduto');

            $where = array( 'UPPER(tipo_produto_nome) = UPPER(?)' => $this->_data['tipo_produto_nome'] );

            if ( !$dbTable->isUnique( $where, $this->_data['tipo_produto_id'] ) ) {

                $this->_message->addMessage( 'Tipo de produto j&aacute; cadastrado com esse nome .', App_Message::ERROR );
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
        $where = array( 'tipo_produto_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'TipoProduto' ), $where );
    }
}