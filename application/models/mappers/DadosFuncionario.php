<?php

/**
 * 
 * @version $Id: DadosFuncionario.php 490 2012-04-04 21:12:15Z helion $
 */
class Model_Mapper_DadosFuncionario extends App_Model_Mapper_Abstract
{
    
    public function getFuncionarioTimeline()
    {

	$dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
	$dbTimeline         = App_Model_DbTable_Factory::get( 'Timeline' );

	$select = $dbDadosFuncionario->select()
		->setIntegrityCheck( false )
		->from(
			array('f' => $dbDadosFuncionario), array('f.*')
		)
		->join(
		array('t' => $dbTimeline), 'f.funcionario_id  = t.funcionario_id ', 
                        array()
	);

	$rows = $dbDadosFuncionario->fetchAll( $select );
        
        $data = array( '' => null );
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                $data[$row->funcionario_id] = $row->dados_func_nome;   
            }
        }
	return $data;
    }
}