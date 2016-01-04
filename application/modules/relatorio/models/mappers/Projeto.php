<?php

/**
 * 
 * @version $Id $
 */
class Relatorio_Model_Mapper_Projeto extends App_Model_Mapper_Abstract 
{
	/**
	 * @access public
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchAll()
	{
		$dbTimeLine         = App_Model_DbTable_Factory::get( 'Timeline' );
		$dbProjeto 	    = App_Model_DbTable_Factory::get( 'Projeto' );
		$dbAtividade 	    = App_Model_DbTable_Factory::get( 'Atividade' );
		$dbFuncionario      = App_Model_DbTable_Factory::get( 'Funcionario' );
		$dbUsuario 	    = App_Model_DbTable_Factory::get( 'Usuario' );
		$dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
		
		$select = $dbTimeLine->select()
			->setIntegrityCheck( false )
			->from(
				array( 't' => $dbTimeLine ),
				array( 'timeline_inicio', 'timeline_fim', 'timeline_carga_horaria' )
			)
			->join(
				array( 'p' => $dbProjeto ),
				'p.projeto_id = t.projeto_id',
				array( 'projeto_id', 'projeto_nome' )
			)
			->join(
				array( 'a' => $dbAtividade ),
				'a.atividade_id = t.atividade_id',
				array( 'atividade_id', 'atividade_nome' )
			)
			->join(
				array( 'f' => $dbFuncionario ),
				'f.funcionario_id = t.funcionario_id',
				array( 'funcionario_id', 'funcionario_carga_diaria' )
			)
			->joinLeft(
				array( 'u' => $dbUsuario ),
				'u.usuario_id = f.usuario_id',
				array()
			)
			->joinLeft(
				array( 'df' => $dbDadosFuncionario ),
				'df.funcionario_id = f.funcionario_id',
				array(
					'funcionario_nome' => new Zend_Db_Expr( 'IFNULL( u.usuario_nome, df.dados_func_nome )' ),
				)
			)
			->where( '((DATE(?) BETWEEN DATE(t.timeline_inicio) AND DATE(t.timeline_fim)', $this->_data['dt_start'] )
			->orWhere( 'DATE(?) BETWEEN DATE(t.timeline_inicio) AND DATE(t.timeline_fim))', $this->_data['dt_end'] )
			->orWhere( '(DATE(?) <= DATE(t.timeline_inicio)', $this->_data['dt_start'] )
			->where( 'DATE(?) >= DATE(t.timeline_fim)))', $this->_data['dt_end'] );
		
		//Filtra por projeto
		if ( !empty($this->_data['projeto_id']) ) 
		    $select->where( 'p.projeto_id = ?', $this->_data['projeto_id'] );
		
		//Filtra por funcionario
		if ( !empty($this->_data['funcionario_id']) )
		    $select->where( 'f.funcionario_id = ?', $this->_data['funcionario_id'] );

		//Filtra por atividade
		if ( !empty($this->_data['atividade_id']) )
		    $select->where( 'a.atividade_id = ?', $this->_data['atividade_id'] );
		
		$rows = $dbTimeLine->fetchAll( $select );
		
		return $rows;
	}
	
	/**
	 * @access public
	 * @return array
	 */
	public function projeto()
	{
		$rows = $this->fetchAll();
		
		return $this->_getDataReport( $rows );
	}
	
	/**
	 * @acces protected
	 * @param Zend_Db_Table_Rowset $rows
	 * @return array
	 */
	protected function _getDataReport( $rows )
	{
	    $carga_horaria 	= array();
	    $data 			= array();
	    
	    foreach ( $rows as $row ) {

	    	//Carga Horaria
	    	$key = array(
    			'projeto_id' 		=> $row->projeto_id,
    			'funcionario_id' 	=> $row->funcionario_id,
    			'atividade_id' 		=> $row->atividade_id
	    	);
	    	
	    	$indice = serialize( $key );
	    	
	    	if ( empty($carga_horaria[$indice]) )
	    		$carga_horaria[$indice] = $this->_getCargaHoraria( $row );
	    	else
	    		$carga_horaria[$indice] += $this->_getCargaHoraria( $row );
	    	
	    	//Projeto
	    	$data[$row->projeto_id] = array(
	    		'value' => $row->projeto_nome,
	    		'data' 	=> array()
	    	);
	    	
	    	//Funcionario
	    	$data[$row->projeto_id]['data'][$row->funcionario_id] = array(
	    		'value' => $row->funcionario_nome,
	    		'data' 	=> array()
	    	);
	    	 
	    }
	    
	    //Atividade
	    foreach ( $rows as $row ) {
	    	
	    	$key = array(
    			'projeto_id' 		=> $row->projeto_id,
    			'funcionario_id' 	=> $row->funcionario_id,
    			'atividade_id' 		=> $row->atividade_id
	    	);
	    	
	    	$indice = serialize( $key );
	    	
	    	$data[$row->projeto_id]['data'][$row->funcionario_id]['data'][$row->atividade_id] = array(
	    		'descricao' 	=> $row->atividade_nome,
	    		'carga_horaria' => $carga_horaria[$indice] 
	    	);
	    	
	    }
	    
	    return $data;
	}
	
	/**
	 * 
	 * @access protected
	 * @param Zend_Db_Table_Row $row
	 * @param array $carga_horaria
	 * @return stdClass
	 */
	protected function _getItens( $row, $carga_horaria )
	{
    	$indice = $this->_getIndice( $row );
    	
    	$std = new stdClass;
	    
	    $std->funcionario_nome 	= $row->funcionario_nome;
	    $std->atividade_nome 	= $row->atividade_nome;
	    $std->carga_horaria 	= $carga_horaria[$indice];
	    
	    return $std;
	}
	
	/**
	 * Retorna a carga horaria de acordo com o periodo
	 * 
	 * @access protected
	 * @param Zend_Db_Table_Row $row
	 * @return int
	 */
	protected function _getCargaHoraria( $row )
	{
		//Perido do projeto
		$dtDbStart 	= strtotime( $row->timeline_inicio );
		$dtDbEnd 	= strtotime( $row->timeline_fim );
		
		//Periodo do filtro
		$dtStart 	= strtotime( $this->_data['dt_start'] );
		$dtEnd 		= strtotime( $this->_data['dt_end'] );
		
		//Defina qual sera a data inicial
		$start 	= $dtDbStart > $dtStart ? $dtDbStart : $dtStart;
		$end 	= $dtDbEnd > $dtEnd ? $dtEnd : $dtDbEnd;
		
		//Diferenca em dias
		$diff = intval( ($end - $start) / (24 * 60 * 60) );
		
		switch ( $diff ) {
			
			case 0:
				//$carga_horaria = $row->funcionario_carga_diaria;
				$carga_horaria = $row->timeline_carga_horaria;
				break;
				
			case 1:
				//$carga_horaria = 2 * $row->funcionario_carga_diaria;
				$carga_horaria = 2 * $row->timeline_carga_horaria;
				break;
				
			default:
				//$carga_horaria = $diff * $row->funcionario_carga_diaria;
				$carga_horaria = $diff * $row->timeline_carga_horaria;
				
		}
		
		return $carga_horaria;
	}
	
	/**
	 * @access protected
	 * @param Zend_Db_Table_Row $row
	 * @return string
	 */
	protected function _getIndice( $row )
	{
		$key = array(
				'projeto_id' 		=> $row->projeto_id,
				'funcionario_id' 	=> $row->funcionario_id,
				'atividade_id' 		=> $row->atividade_id
		);
		
		$indice = serialize( $key );
		
		return $indice;
	}
}