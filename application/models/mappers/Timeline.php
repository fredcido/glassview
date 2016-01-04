<?php

/**
 * 
 * @version $Id: Timeline.php 819 2012-10-08 20:18:03Z fred $
 */
class Model_Mapper_Timeline extends App_Model_Mapper_Abstract
{
    /**
     * 
     */
    public function save()
    {
        try {
            
            $this->_data['timeline_inicio'] = $this->_data['dt_inicio'] . ' ' . str_replace('T', '', $this->_data['hr_inicio']);
            $this->_data['timeline_fim']    = $this->_data['dt_fim'] . ' ' . str_replace('T', '', $this->_data['hr_fim']);
            
            unset( $this->_data['dt_inicio'] );
            unset( $this->_data['hr_inicio'] );
            unset( $this->_data['dt_fim'] );
            unset( $this->_data['hr_fim'] );
            
            $dbTimeline = App_Model_DbTable_Factory::get( 'Timeline' );
            
            return parent::_simpleSave( $dbTimeline );
            
        } catch ( Exception $e ) {
            
            return false;
            
        }
    }
    
    /**
     * 
     */
    public function fetchGrid( $projeto_id = null )
    {
        $dbTimeline          = App_Model_DbTable_Factory::get( 'Timeline' );
        $dbProjeto           = App_Model_DbTable_Factory::get( 'Projeto' );
        $dbAtividade         = App_Model_DbTable_Factory::get( 'Atividade' );
        $dbDadosFuncionario  = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
        
        $select = $dbTimeline->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('t' => $dbTimeline),
                        array('t.*')
                )
                ->join(
                        array('p' => $dbProjeto),
                        'p.projeto_id = t.projeto_id',
                        array('p.projeto_nome')
                )
                ->join(
                        array('a' => $dbAtividade),
                        'a.atividade_id = t.atividade_id',
                        array('a.atividade_nome')
                )
                ->join(
                        array('df' => $dbDadosFuncionario),
                        'df.funcionario_id = t.funcionario_id',
                        array('df.dados_func_nome')
                );
	
	if ( !empty( $projeto_id ) )
	    $select->where( 'p.projeto_id = ?', $projeto_id );
	        
        $rows = $dbTimeline->fetchAll( $select );

        $data = array();
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        if ( $rows->count() ) {
                
            foreach ( $rows as $row ) {
                
                $textEvetn = '<b>'.$translate->_('Projeto').':</b> '.$row->projeto_nome 
                            .'<br><b>'.$translate->_('Atividade').':</b> ' . $row->atividade_nome
                            .'<br><b>'.$translate->_('Funcionário').':</b> ' . $row->dados_func_nome;
                    
                $data[] = array( 
                    'id'            => $row->timeline_id,
                    'start_date'    => $row->timeline_inicio,
                    'end_date'      => $row->timeline_fim,
                    'text'          => $textEvetn,
                    'data'          => $row->toArray()
                );

            }
        }

        return $data;
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'timeline_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Timeline' ), $where );
    }
    
    public function fetchFuncionarioByProjeto()
    {
        $dbTimeline = App_Model_DbTable_Factory::get( 'Timeline' );
        $dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
        
        if ( !empty($this->_data['projeto_id']) ) {
        
        $select = $dbTimeline->select()
            ->setIntegrityCheck( false )
            ->from(
                array( 't' => $dbTimeline ),
                array()
            )
            ->join(
                array( 'f' => $dbDadosFuncionario ),
                't.funcionario_id = f.funcionario_id',
                array('funcionario_id', 'dados_func_nome')
            )
            ->where( 't.projeto_id = ?', $this->_data['projeto_id'] )
            ->group( 'f.funcionario_id' );
            
        $rows = $dbTimeline->fetchAll( $select );
        
        $data = array();

        if ( $rows->count() ) {

            $data[] = array('id' => 0, 'name' => '');

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->funcionario_id,
                    'name'  => $row->dados_func_nome
                );

            }

        }

        }
        
        $result = array('identifier' => 'id', 'label' => 'name', 'items' => $data);
        
        return $result;
    }

    public function fetchPeriodo()
    {
        $dbTimeline = App_Model_DbTable_Factory::get( 'Timeline' );
        
        $select = $dbTimeline->select()
            ->from( 
                array( 't' => $dbTimeline ),
                array(
                    'dt_inicio' => new Zend_Db_Expr("DATE_FORMAT(MIN(t.timeline_inicio), '%Y-%m-%d')"),
                    'dt_fim' => new Zend_Db_Expr("DATE_FORMAT(MAX(t.timeline_fim), '%Y-%m-%d')"),
                )
            )
            ->where('t.projeto_id = ?', $this->_data['projeto_id'])
            ->where('t.funcionario_id = ?', $this->_data['funcionario_id']);

        $row = $dbTimeline->fetchRow( $select );
        
        return $row; 
    }
    
    public function calcularCusto() 
    {
        $dbFuncionario = App_Model_DbTable_Factory::get( 'Funcionario' );
        
        $select = $dbFuncionario->select()
            ->from(
                array('f' => $dbFuncionario),
                array(
                    'funcionario_salario',
                    'funcionario_carga_diaria'
                )
            )
            ->where('f.funcionario_id = ?', $this->_data['funcionario_id']);
            
        $row = $dbFuncionario->fetchRow( $select );
        
        $valoHora = $row->funcionario_salario / 30 / $row->funcionario_carga_diaria;
        
	if ( empty( $this->_data['qtd_dias'] ) )
	    $this->_data['qtd_dias'] = 1;
	
        $data = array(
            'custo'         => $this->_data['qtd_dias'] * $valoHora * $row->funcionario_carga_diaria,
            'carga_horaria' => $this->_data['qtd_dias'] * $row->funcionario_carga_diaria
        );
        
        return $data;
    }
    
    public function buscaMaxMimAtividades() 
    {
        $dbTimeline = App_Model_DbTable_Factory::get( 'Timeline' );
        
        $select = $dbTimeline->select()
            ->from(
                array('t' => $dbTimeline),
                array(
                    'min' => "DATE_FORMAT( MIN(timeline_inicio), '%Y-%m-%d')",
                    'max' => "DATE_FORMAT( MAX(timeline_fim), '%Y-%m-%d')"
                )
            )
            ->where('t.funcionario_id = ?', $this->_data['funcionario_id']);

        $row = $dbTimeline->fetchRow( $select );
        
        $data = array(
            'result' => true, 
            'min'    => $row->min,
            'max'    => $row->max
        );
        
        return $data;
    }
        
    /**
     * 
     * @access public
     * @return Zend_Db_Table_Rowset
     */
    public function fetchLancamentos()
    {
        $dbTimeline 		= App_Model_DbTable_Factory::get( 'Timeline' );
        $dbProjeto 			= App_Model_DbTable_Factory::get( 'Projeto' );
        $dbAtividade 		= App_Model_DbTable_factory::get( 'Atividade' );
        $dbFuncionario 		= App_Model_DbTable_factory::get( 'Funcionario' );
        
        //Dados da timeline
        $subSelect = $dbTimeline->select()
        	->setIntegrityCheck( false )
        	->from(
				$dbTimeline,
        		array(
        			'projeto_id',
        			'funcionario_id',
        			'atividade_id',
        			'total' => new Zend_Db_Expr( 'COUNT(1)' )
				)
			)
			->where( 'funcionario_id = ?', $this->_data['funcionario_id'] )
			->where( '((DATE(?) BETWEEN DATE(timeline_inicio) AND DATE(timeline_fim)', $this->_data['dt_inicio'] )
			->orWhere( 'DATE(?) BETWEEN DATE(timeline_inicio) AND DATE(timeline_fim))', $this->_data['dt_fim'] )
			->orWhere( '(DATE(?) <= DATE(timeline_inicio)', $this->_data['dt_inicio'] )
			->where( 'DATE(?) >= DATE(timeline_fim)))', $this->_data['dt_fim'] )
			->group( 'projeto_id' );
        
        $select = $dbTimeline->select()
        	->setIntegrityCheck( false )
        	->from(
        		array( 't' => new Zend_Db_Expr( '(' . $subSelect . ')') ),
        		array(
        			'projeto_id',
					'funcionario_id',
        			new Zend_Db_Expr( 'MAX(total)' )
        		)
        	)
        	->join(
        		array( 'p' => $dbProjeto ),
        		'p.projeto_id = t.projeto_id',
        		array( 'projeto_nome' )
        	)
        	->join(
        		array( 'a' => $dbAtividade ),
        		'a.atividade_id = t.atividade_id',
        		array( 'atividade_nome' )
        	)
        	->group( 'projeto_id' );
        
        $rows = $dbTimeline->fetchAll( $select );
        
        //Dados funcionario
        $select = $dbFuncionario->select()
        	->from(
        		$dbFuncionario,
        		array( 
        			'salario' 		=> 'funcionario_salario',
        			'carga_mensal' 	=> 'funcionario_carga_mensal' 
        		) 
        	)
        	->where( 'funcionario_id = ?', $this->_data['funcionario_id'] );
        
        $data = $dbFuncionario->fetchRow( $select )->toArray();
        
        $result = array( 'rows' => array() );
        
        foreach ( $rows as $row ) {
        	
        	//Carga horaria
        	$carga_horaria 		= $this->_getCargaHorariaTimeline( $row );
        	$perc_carga_horaria = floor($carga_horaria * 100 / $data['carga_mensal']);
        	
        	//Salario
        	$salario = $carga_horaria * $data['salario'] / $data['carga_mensal']; 
        	
        	$result['rows'][] = array(
				'id' 	=> $row->projeto_id,
        		'data' 	=> array(
	        		$row->projeto_nome,
	        		$row->atividade_nome,
	        		$carga_horaria,
	        		$perc_carga_horaria . '%',
        			number_format( $salario, 2, ',', '.' )
        		)
        	);
        	
        }
        
        return $result;
        	
    }
    
    /**
     * 
     * @access protected
     * @param 	Zend_Db_Table_Row $data
     * @return int
     */
    protected function _getCargaHorariaTimeline( $data )
    {
    	$dbTimeline = App_Model_DbTable_factory::get( 'Timeline' );
    	
    	$select = $dbTimeline->select()
    		->from(
    			$dbTimeline,
    			array(
					'diff' => new Zend_Db_Expr( 'DATEDIFF(timeline_fim, timeline_inicio)' ),
    				'timeline_carga_horaria'
    			)		
    		)
    		->where( 'projeto_id = ?', $data->projeto_id )
    		->where( 'funcionario_id = ?', $data->funcionario_id )
    		->where( '((DATE(?) BETWEEN DATE(timeline_inicio) AND DATE(timeline_fim)', $this->_data['dt_inicio'] )
			->orWhere( 'DATE(?) BETWEEN DATE(timeline_inicio) AND DATE(timeline_fim))', $this->_data['dt_fim'] )
			->orWhere( '(DATE(?) <= DATE(timeline_inicio)', $this->_data['dt_inicio'] )
			->where( 'DATE(?) >= DATE(timeline_fim)))', $this->_data['dt_fim'] );
    	
    	$rows = $dbTimeline->fetchAll( $select );
    	
    	$cargaHoraria = array();
    	
    	foreach ( $rows as $row ) {
    		
    		switch ( $row->diff ) {
    			
    			case 0:
    				array_push( $cargaHoraria, $row->timeline_carga_horaria );
    				break;
    				
    			case 1:
    				$value = 2 * $row->timeline_carga_horaria;
    				array_push( $cargaHoraria, $value );
    				break;
    				
    			default:
    				$value = $row->diff * $row->timeline_carga_horaria;
    				array_push( $cargaHoraria, $value );
    		}
    		
    	}
    	
    	return array_sum( $cargaHoraria );
    }
}