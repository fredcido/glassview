<?php

/**
 * 
 * @version $Id: Filial.php 269 2012-02-16 19:01:31Z fred $
 */
class Model_Mapper_Filial extends App_Model_Mapper_Abstract
{
     public function fetchGrid()
    {
        $dbFilial = App_Model_DbTable_Factory::get('Filial');
        $dbPais   = App_Model_DbTable_Factory::get('Pais');
        $dbEstado = App_Model_DbTable_Factory::get('Estado');
        $dbCidade = App_Model_DbTable_Factory::get('Cidade');
        
        $select = $dbFilial->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('f' => $dbFilial),
                        array('f.*')
                )
                ->join(
                        array('p' => $dbPais),
                        'p.pais_id = f.pais_id',
                        array('p.pais_nome')
                )
                ->join(
                        array('e' => $dbEstado),
                        'e.estado_id = f.estado_id',
                        array('e.estado_nome')
                )
                ->join(
                        array('c' => $dbCidade),
                        'c.cidade_id = f.cidade_id',
                        array('c.cidade_nome')
                )
		->order( 'filial_nome' );
        
        $rows = $dbFilial->fetchAll( $select );
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->filial_id,
                    'data'  => array(
                        ++$key,
                        $row->filial_nome,
                        $row->cidade_nome,
                        $row->estado_nome,
                        parent::_showStatus($row->filial_status)
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

	    $dbTable = App_Model_DbTable_Factory::get('Filial');

	    $where = array( 
			'UPPER(filial_nome) = UPPER(?)' => $this->_data['filial_nome'],
			'pais_id = ?'			=> $this->_data['pais_id'],
			'estado_id = ?'			=> $this->_data['estado_id'],
			'cidade_id = ?'			=> $this->_data['cidade_id'],
		    );

	    if ( !$dbTable->isUnique( $where, $this->_data['filial_id'] ) ) {

		$this->_message->addMessage( 'Filial j&aacute; cadastrada para essa localidade.', App_Message::ERROR );
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
        $where = array( 'filial_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Filial' ), $where );
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function buscaEstados()
    {
        $dbEstado = App_Model_DbTable_Factory::get('Estado');
        
        $select = $dbEstado->select('estado_id','estado_sigla')
                            ->where('pais_id = :pais_id')
                            ->bind(
                                    array(
                                           ':pais_id' => $this->_data['pais']
                                    )
				);
                
        return $dbEstado->fetchAll( $select );
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function buscaCidade()
    {
        $dbCidade = App_Model_DbTable_Factory::get('Cidade');
        
        $select = $dbCidade->select('cidade_id','cidade_nome')
                            ->where('estado_id = :estado_id')
                            ->bind(
                                    array(
                                           ':estado_id' => $this->_data['estado']
                                    )
				);
                
        return $dbCidade->fetchAll($select);
    }
}