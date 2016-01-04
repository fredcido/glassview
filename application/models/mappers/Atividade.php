<?php

/**
 * 
 * @version $Id: Atividade.php 421 2012-03-09 01:00:07Z helion $
 */
class Model_Mapper_Atividade extends App_Model_Mapper_Abstract
{

    public function fetchGrid()
    {
        $dbProjeto = App_Model_DbTable_Factory::get('Atividade');

        $rows = $dbProjeto->fetchAll();

        $data = array( 'rows' => array() );

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->atividade_id,
                    'data'  => array(
                        ++$key,
                        $row->atividade_nome,
                        $row->atividade_descricao
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


	    $dbTable = App_Model_DbTable_Factory::get( 'Atividade' );

	    $where = array( 'UPPER(atividade_nome) = UPPER(?)' => $this->_data['atividade_nome'] );

	    if ( !$dbTable->isUnique( $where, $this->_data['atividade_id'] ) ) {

		$this->_message->addMessage( 'Atividade j&aacute; cadastrado com esse nome.', App_Message::ERROR );
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
        $where = array( 'atividade_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Atividade' ), $where );
    }
}