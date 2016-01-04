<?php

/**
 * 
 * @version $Id: Terceiro.php 232 2012-02-14 18:27:02Z fred $
 */
class Model_Mapper_Terceiro extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        $dbTerceiro = App_Model_DbTable_Factory::get('Terceiro');
        
        $rows = $dbTerceiro->fetchAll();
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                switch ($row->terceiro_tipo) {
                    case 'C':
                        $tipo = 'Cliente';
                        break;
                    case 'F':
                        $tipo = 'Fornecedor';
                        break;
                    case 'M':
                        $tipo = 'Mantenedor';
                        break;
                    default:
                        $tipo = '';
                        break;
                }
                
                switch ($row->terceiro_pessoa) {
                    case 'F':
                        $pessoa = 'Física';
                        break;
                    case 'J':
                        $pessoa = 'Jurídica';
                        break;
                    default:
                        $pessoa = '';
                        break;
                }
                
                $data['rows'][] = array(
                    'id'    => $row->terceiro_id,
                    'data'  => array(
                        ++$key,
                        $row->terceiro_nome,
                        $tipo,
                        $pessoa,
                        $row->terceiro_cpf_cnpj,
                        $row->terceiro_contato,
                        $row->terceiro_telefone,
                        $row->terceiro_fax
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
            
	    $dbTable = App_Model_DbTable_Factory::get('Terceiro');

	    $where = array( 
		'UPPER(terceiro_nome) = UPPER(?)' => $this->_data['terceiro_nome'],
		'terceiro_tipo = ?'		  => $this->_data['terceiro_tipo'],
		'terceiro_pessoa = ?'		  => $this->_data['terceiro_pessoa']
	    );

	    if ( !$dbTable->isUnique( $where, $this->_data['terceiro_id'] ) ) {

		$this->_message->addMessage( 'Terceiro j&aacute; cadastrado com esses dados.', App_Message::ERROR );
		return false;
	    }	
	    
            return parent::_simpleSave( $dbTable );
            
        } catch (Exception $e) {

            return false;
            
        }
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Terceiro' ),
                                        array( 'terceiro_id = ?' => $this->_data['id'] ));
    }
}