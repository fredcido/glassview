<?php

/**
 * 
 * @version $Id: Tela.php 289 2012-02-19 19:35:08Z fred $
 */
class Model_Mapper_Tela extends App_Model_Mapper_Abstract
{
    public function fetchGrid()
    {
        $dbTela = App_Model_DbTable_Factory::get('Tela');
        $dbModulo = App_Model_DbTable_Factory::get('Modulo');
        
        $select = $dbTela->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('t' => $dbTela),
                        array('t.*')
                )
                ->join(
                        array('m' => $dbModulo),
                        'm.modulo_id = t.modulo_id',
                        array('m.modulo_nome')
                )
		->order( 'tela_nome' );
        
        $rows = $dbTela->fetchAll( $select );
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->tela_id,
                    'data'  => array(
                        ++$key,
                        $row->tela_nome,
                        $row->modulo_nome,
                        $row->tela_path,
                        parent::_showStatus($row->tela_status)
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
            
            $dbTable = App_Model_DbTable_Factory::get('Tela');

            $where = array( 'UPPER(tela_path) = UPPER(?)' => $this->_data['tela_path'] );

            if ( !$dbTable->isUnique( $where, $this->_data['tela_id'] ) ) {

                $this->_message->addMessage( 'Tela j&aacute; cadastrado com esse path .', App_Message::ERROR );
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
        $where = array( 'tela_id =?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Tela' ), $where );
    }
}