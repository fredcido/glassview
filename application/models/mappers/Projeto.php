<?php

/**
 * 
 * @version $Id: Projeto.php 569 2012-05-10 15:13:49Z ze $
 */
class Model_Mapper_Projeto extends App_Model_Mapper_Abstract
{

    public function fetchGrid()
    {
        $dbProjeto = App_Model_DbTable_Factory::get('Projeto');

        $rows = $dbProjeto->fetchAll();

        $data = array( 'rows' => array() );

        $date = new Zend_Date();

        if ( $rows->count() ) {

	    $currency = new Zend_Currency();
	    
            foreach ( $rows as $key => $row ) {

                if( !empty( $row->projeto_inicio )){
                    
                    $date->set( $row->projeto_inicio );
                    $dataInicio = $date->toString( 'dd/MM/yyyy' );
                }else{
                    
                    $dataInicio = '-';
                }
                if( !empty( $row->projeto_final )){
                    
                    $date->set( $row->projeto_final );
                    $dataFinal = $date->toString( 'dd/MM/yyyy' );
                }else{
                    
                    $dataFinal = '-';
                }

                $data['rows'][] = array(
                    'id'    => $row->projeto_id,
                    'data'  => array(
                        ++$key,
                        $row->projeto_nome,
                        $row->projeto_descricao,
                        $currency->setValue( $row->projeto_orcamento )->toString(),
                        $dataInicio,
                        $dataFinal,
                        $this->_getStatusProjeto( $row->projeto_status )
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

	    $dbTable = App_Model_DbTable_Factory::get( 'Projeto' );

	    $where = array( 'UPPER(projeto_nome) = UPPER(?)' => $this->_data['projeto_nome'] );

	    if ( !$dbTable->isUnique( $where, $this->_data['projeto_id'] ) ) {

		$this->_message->addMessage( 'Projeto j&aacute; cadastrado com esse nome.', App_Message::ERROR );
		return false;
	    }

	    return parent::_simpleSave( $dbTable );

        } catch ( Exception $e ) {
	    Zend_Debug::dump($e);
	    exit;
            return false;
        }
    }

    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'projeto_id  = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Projeto' ), $where );
    }
	
	public function fetchAll()
	{
		$dbProjeto = App_Model_DbTable_Factory::get('Projeto');
		
		$rows = $dbProjeto->fetchAll( array('projeto_status = ?' => 'I'), 'projeto_nome' );
		
		return $rows;
	}    
    
    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getStatusProjeto( $type )
    {
	$optTipo['P'] = 'Pendente';
	$optTipo['I'] = 'Iniciado';
	$optTipo['F'] = 'Finalizado';
	$optTipo['C'] = 'Cancelado';

	return empty( $optTipo[ $type ] ) ? '' : $optTipo[ $type ];
    }
}