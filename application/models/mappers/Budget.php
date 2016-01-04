<?php

/**
 * 
 * @version $Id: Budget.php 260 2012-02-15 17:58:40Z fred $
 */
class Model_Mapper_Budget extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return boolean
     */
    public function save()
    {
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	
	$dbBudget->getAdapter()->beginTransaction();
        try {
	    
	    $dataForm = $this->_data;

	    // salva o budget
	    $dataForm['fn_budget_id'] = $this->_saveBudget( $dataForm );
	    
	    // salva budget mes
	    $dataForm['fn_budget_mes_id'] = $this->_saveBudgetMes( $dataForm );
	    
	    // salva previsao para o mes
	    $dataForm['fn_budget_previsao_id'] = $this->_saveBudgetPrevisao( $dataForm );
	    
	    // Pega total do Budget
	    $totalBudget = (float)$this->somaTotalBudget( $dataForm['fn_budget_id'] );
	    
	    // Atualiza total do Budget na base
	    $whereBudget = $dbBudget->getAdapter()->quoteInto( 'fn_budget_id = ?', $dataForm['fn_budget_id'] );
	    $dbBudget->update( array( 'fn_budget_total' => $totalBudget ), $whereBudget );
	    
	    // Soma total do Mes/Ano para o Budget
	    $totalBudgetMes = (float)$this->somaTotalBudgetMes( $dataForm['fn_budget_id'], $dataForm['fn_budget_mes_id'] );
	    
	    $whereBudgetMes = $dbBudget->getAdapter()->quoteInto( 'fn_budget_mes_id = ?', $dataForm['fn_budget_mes_id'] );
	    $dbBudgetMes->update( array( 'fn_budget_mes_total' => $totalBudgetMes ), $whereBudgetMes );
	    
	    // Calcula o total da categoria para atualizar
	    $totalCategoria = (float)$this->getTotalBudgetPorCategoria( $dataForm['projeto_id'], $dataForm['fn_categoria_id'], $dataForm['budget_ano'] );
	    
	    $dbBudget->getAdapter()->commit();
	    
	    return array(
		'status'		    => true,
		'fn_budget_id'		    => $dataForm['fn_budget_id'],
		'fn_budget_total_categoria' => $totalCategoria,
		'fn_budget_total'	    => $totalBudget,
		'total_bugdet_geral'	    => $this->valorGeralTipoLancamento( $dataForm )->total
	    );
	    
        } catch ( Exception $e ) {
	    
	    $dbBudget->getAdapter()->rollBack();
	    
	    return array( 'status' => false );
        }
    }
    
    
    /**
     *
     * @param array $data
     * @return int 
     */
    protected function _saveBudget( $data )
    {
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	
	// Verifica se o budget ja existe, caso nao, insere
	if ( empty( $data['fn_budget_id'] ) ) {

	   $data['fn_budget_total'] = $data['valor_tipo_lanc'];

	   $data['fn_budget_id'] = parent::_simpleSave( $dbBudget );
	}
	    
	return $data['fn_budget_id'];
    }
    
    /**
     *
     * @param array $data
     * @return int 
     */
    protected function _saveBudgetMes( $data )
    {
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	
	// Verifica se ja tem budget para aquele mes
	$budgetMes = $this->_verificaBudgetMes( $data['fn_budget_id'], $data['mes'], $data['budget_ano'] );
	if ( empty( $budgetMes ) ) {

	    $this->_data = array(
		'fn_budget_id'		=> $data['fn_budget_id'],
		'fn_budget_mes_data'	=> implode( '-', array( $data['budget_ano'], $data['mes'], '01' ) ),
		'fn_budget_mes_total'	=> $data['valor_tipo_lanc']
	    );

	    $data['fn_budget_mes_id'] = parent::_simpleSave( $dbBudgetMes );
	} else
	    $data['fn_budget_mes_id'] = $budgetMes->fn_budget_mes_id;
	
	return $data['fn_budget_mes_id'];
    }
    
    /**
     *
     * @param array $data
     * @return int 
     */
    protected function _saveBudgetPrevisao( $data )
    {
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	
	$this->_data = array(
	    'fn_budget_mes_id'		=> $data['fn_budget_mes_id'],
	    'fn_tipo_lanc_id'		=> $data['fn_tipo_lanc_id'],
	    'fn_budget_previsao_valor'	=> $data['valor_tipo_lanc']
	);
	
	// Verifica se ja tem previsao para aquele tipo de lancamento para aquele mes/ano
	$budgetPrevisao = $this->_verificaBudgetPrevisao( $data['fn_budget_mes_id'], $data['fn_tipo_lanc_id'] );
	
	if ( !empty( $budgetPrevisao ) )
	    $this->_data['fn_budget_previsao_id'] = $budgetPrevisao->fn_budget_previsao_id;
	
	$data['fn_budget_previsao_id'] = parent::_simpleSave( $dbBudgetPrevisao );
	
	return $data['fn_budget_previsao_id'];
    }
    
    /**
     *
     * @param int $budget_id
     * @return float 
     */
    public function somaTotalBudget( $budget_id )
    {
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	
	$select = $dbBudget->select()
			   ->setIntegrityCheck( false )
			   ->from( array( 'b' => $dbBudget ), array() )
			   ->join(
				array( 'bm' => $dbBudgetMes ),
				'bm.fn_budget_id = b.fn_budget_id',
				array()
			   )
			   ->join(
				array( 'bp' => $dbBudgetPrevisao ),
				'bp.fn_budget_mes_id = bm.fn_budget_mes_id',
				array( 'total' => new Zend_Db_Expr( 'SUM( bp.fn_budget_previsao_valor )' ) )
			   )
			   ->where( 'b.fn_budget_id = ?', $budget_id );
	
	$row = $dbBudget->fetchRow( $select );
	
	return $row->total;
    }
    
    /**
     *
     * @param int $budget_id
     * @param int $mes_id
     * @return float 
     */
    public function somaTotalBudgetMes( $budget_id, $mes_id )
    {
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	
	$select = $dbBudgetMes->select()
			      ->setIntegrityCheck( false )
			      ->from( array( 'bm' => $dbBudgetMes ), array() )
			      ->join(
				array( 'bp' => $dbBudgetPrevisao ),
				'bp.fn_budget_mes_id = bm.fn_budget_mes_id',
				array( 'total' => new Zend_Db_Expr( 'SUM( bp.fn_budget_previsao_valor )' ) )
			      )
			      ->where( 'bm.fn_budget_mes_id = ?', $mes_id )
			      ->where( 'bm.fn_budget_id = ?', $budget_id );
	
	$row = $dbBudgetMes->fetchRow( $select );
	
	return $row->total;
		
    }
    
    /**
     *
     * @param int $budget
     * @param int $mes
     * @param int $ano
     * @return Zend_Db_Table_Row 
     */
    protected function _verificaBudgetMes( $budget, $mes, $ano )
    {
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	
	$select = $dbBudgetMes->select()
			      ->from( $dbBudgetMes )
			      ->where( 'fn_budget_id = ?', $budget )
			      ->where( 'MONTH( fn_budget_mes_data ) = ?', $mes )
			      ->where( 'YEAR( fn_budget_mes_data ) = ?', $ano );
	
	return $dbBudgetMes->fetchRow( $select );
    }
    
    /**
     *
     * @param int $budget_mes
     * @param int $tipo_lancamento
     * @return Zend_Db_Table_Row 
     */
    protected function _verificaBudgetPrevisao( $budget_mes, $tipo_lancamento )
    {
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	
	$select = $dbBudgetPrevisao->select()
				   ->from( $dbBudgetPrevisao )
				   ->where( 'fn_budget_mes_id = ?', $budget_mes )
				   ->where( 'fn_tipo_lanc_id = ?', $tipo_lancamento );
	
	return $dbBudgetPrevisao->fetchRow( $select );
    }
    
    /**
     *
     * @param int $projeto_id
     * @return array 
     */
    public function verificaBudget( $projeto_id )
    {
	$dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	
	$projeto = $dbProjeto->fetchRow( array( 'projeto_id = ?' => $projeto_id ) );
	$budget = $dbBudget->fetchRow( array( 'projeto_id = ?' => $projeto_id ) );
	
	$retorno = array(
	    'projeto'	=> $projeto->toArray(),
	    'permissao' => $this->_session->acl->isAllowedToolbar( App_Plugins_Acl::getIdentifier( '/financeiro/budget/', 'Salvar' ) ) 
	);
	
	if ( !empty( $budget ) )
	    $retorno['budget'] = $budget->toArray();
	    
	return $retorno;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function listaLancamentos( $data )
    {
	$meses = $this->_getMesesBudget( $data['ano'], $data['projeto_inicio'], $data['projeto_final'] );
	
	return array(
	    'meses_keys'	=> array_keys( $meses ),
	    'meses'		=> $meses,
	    'total_categoria'	=> (float)$this->getTotalBudgetPorCategoria( $data['projeto'], $data['categoria'] ),
	    'rows'		=> $this->getGridLancamentosBudget( $data['ano'], array_keys( $meses ), $data['projeto'], $data['categoria'] )
	);
    }
    
    /**
     *
     * @param int $ano
     * @param array $meses
     * @param int $projeto
     * @param int $categoria
     * @return array
     */
    public function getGridLancamentosBudget( $ano, $meses, $projeto, $categoria )
    {
	// Busca lancamentos com os valores
	$lancamentosBudget = $this->listLancamentosBudget( $ano, $meses, $projeto, $categoria );
	
	$mesesGrid = array();
	foreach ( $meses as $mes )
	    $mesesGrid[(int)$mes] = 0;
	
	// Agrupa lancamentos com os meses
	$lancamentosAgrupados = $this->_agrupaLancamentos( $lancamentosBudget, $mesesGrid );
	
	$mapperTipoLancamento = new Model_Mapper_TipoLancamento();
	$recursiveItens = $mapperTipoLancamento->createRecursiveItens( $lancamentosAgrupados );
	
	// Totaliza os itens agrupadores
	$recursiveItens = $this->_totalizaAgrupadores( $recursiveItens );
		
	return array( 'rows' => $this->_createGridLancamentoBudget( $recursiveItens ) );
    }
 
    /**
     *
     * @param array $itens
     * @param int $mes
     * @return float
     */
    protected function _totalizaAgrupadores( $itens, $mesAtual = false )
    {
	$total = 0;
	foreach ( $itens as $key => $item ) {
	
	    if ( !$mesAtual ) {
		
		// Se nao for agrupador
		if ( empty( $item['fn_tipo_lanc_agrupador'] ) )
		    continue;
				
		foreach ( $item['meses'] as $mes => $total )
		    // Se for agrupador, calcula o total dos filhos
		    $itens[$key]['meses'][$mes] = $this->_totalizaAgrupadores( $item['children'], $mes );
		
	    } else {
		
		// Se for agrupador, soma os agrupadores dos filhos dele
		if ( !empty( $item['fn_tipo_lanc_agrupador'] ) )
		    $itens[$key]['meses'][$mes] = $this->_totalizaAgrupadores( $item['children'], $mesAtual );
		    
		$total += $itens[$key]['meses'][$mesAtual];	
	    }
	}
		
	return $mesAtual ? $total : $itens; 
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    protected function _createGridLancamentoBudget( $data, $tabulacao = array() )
    {
	$currency = new Zend_Currency();
	
	$dataGrid = array();
	foreach ( $data as $row ) {
	    
	    $label = implode( '', $tabulacao ) . $row['fn_tipo_lanc_cod'] . ' ' . $row['fn_tipo_lanc_desc'];
	    
	    if ( !empty( $row['fn_tipo_lanc_agrupador'] ) )
		$label = sprintf( '<b>%s</b>', $label );
	    
	    $geralValue = 0;
	    
	    $columns = array();
	    
	    foreach ( $row['meses'] as $mes ) {
		
		//if ( empty( $row['fn_tipo_lanc_agrupador'] ) )
		    $geralValue += $mes;
		
		$mes = empty( $mes ) ? $mes : $currency->setValue( $mes )->toString();
		
		$columns[] = empty( $row['fn_tipo_lanc_agrupador'] ) ? $mes : sprintf( '<b>%s</b>', $mes ) ;
	    }
	    
	    $geralValue = $currency->setValue( $geralValue )->toString();//empty( $row['fn_tipo_lanc_agrupador'] ) ? $currency->setValue( $geralValue )->toString() : '-';
	    
	    if ( !empty( $row['fn_tipo_lanc_agrupador'] ) )
		$geralValue = sprintf( '<b>%s</b>', $geralValue );
	    
	    array_unshift( $columns, $geralValue );
	    array_unshift( $columns, $label );
	
	    $line = array(
		'id'    => json_encode( $row ),
		'data'  => $columns
	    );
	    
	    $dataGrid[] = $line;
	    
	    if ( !empty( $row['children'] ) ) {
		
		$novaTabulacao = $tabulacao;
		
		$novaTabulacao[] = str_repeat( '&nbsp;', 4 );
		
		$childArray = $this->_createGridLancamentoBudget( $row['children'], $novaTabulacao );
		
		$dataGrid = array_merge( $dataGrid, $childArray );
	    }
	}
	
	return $dataGrid;
    }
    
    /**
     *
     * @param array $lancamentos
     * @param array $mesesGrid
     * @return array
     */
    protected function _agrupaLancamentos( $lancamentos, $mesesGrid )
    {
	$dataFinal = array();
	foreach ( $lancamentos as $lancamento ) {
	    
	    if ( empty( $dataFinal[$lancamento['fn_tipo_lanc_id']] ) ) {
		
		$dataFinal[$lancamento->fn_tipo_lanc_id] = $lancamento->toArray();
		$dataFinal[$lancamento->fn_tipo_lanc_id]['meses'] = $mesesGrid;
	    }
	    
	    if ( empty( $lancamento->fn_tipo_lanc_agrupador ) && !empty( $lancamento->mes ) )
		$dataFinal[$lancamento->fn_tipo_lanc_id]['meses'][$lancamento->mes] = $lancamento->valor;
	}
	
	return $dataFinal;
    }
    
    /**
     *
     * @param int $ano
     * @param array $meses
     * @param int $projeto
     * @param int $categoria
     * @return Zend_Db_Table_Rowset
     */
    public function listLancamentosBudget( $ano, $meses, $projeto, $categoria )
    {
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	
	$select = $dbTipoLancamento->select()
				   ->setIntegrityCheck( false )
				   ->from( 
					array( 'tl' => $dbTipoLancamento ),
					array( 
					    'fn_tipo_lanc_id', 
					    'fn_tipo_lanc_pai',
					    'fn_tipo_lanc_cod',
					    'fn_tipo_lanc_agrupador',
					    'fn_tipo_lanc_desc'
					)
				   )
				   ->joinLeft(
					array( 'bp' => $dbBudgetPrevisao ),
					'bp.fn_tipo_lanc_id = tl.fn_tipo_lanc_id',
					array( 'valor' => 'fn_budget_previsao_valor' )
				   )
				   ->joinLeft(
					array( 'bm' => $dbBudgetMes ),
					'bm.fn_budget_mes_id = bp.fn_budget_mes_id
					AND MONTH( bm.fn_budget_mes_data ) IN (' . implode( ',', $meses ) . ')
					AND YEAR( bm.fn_budget_mes_data ) = ' . $ano,
					array( 'mes' => new Zend_Db_Expr( 'MONTH( bm.fn_budget_mes_data )' ) )
				   )
				   ->joinLeft(
					array( 'b' => $dbBudget ),
					'b.fn_budget_id = bm.fn_budget_id
					 AND b.projeto_id = ' . $projeto,
					array()
				   )
				   ->where( 'tl.fn_categoria_id = ?', $categoria )
				   ->order( array( 'fn_tipo_lanc_pai', 'fn_tipo_lanc_ordem' ) );
	
	return $dbTipoLancamento->fetchAll( $select );
				   
    }
    
    /**
     *
     * @param int $ano
     * @param string $data_inicio
     * @param string $data_fim
     * @return array
     */
    protected function _getMesesBudget( $ano, $data_inicio, $data_fim )
    {
	$dateIni = new Zend_Date( $data_inicio );
	$dateFim = new Zend_Date( $data_fim );
	
	$mesInicial = $dateIni->isEarlier( $ano, Zend_Date::YEAR ) ? 1 : $dateIni->get( Zend_Date::MONTH );
	$mesFinal = $dateFim->isLater( $ano, Zend_Date::YEAR ) ? 12 : $dateFim->get( Zend_Date::MONTH );
	
	$rangeMeses = range( $mesInicial, $mesFinal );
	
	$monthName = new Zend_Date();
	
	$meses = array();
	foreach ( $rangeMeses as $mes ) {
	    
	    $monthName->set( $mes, Zend_Date::MONTH );
	    $meses[$mes] = ucfirst( $monthName->toString( 'MMMM' ) );
	}
	
	return $meses;
    }
    
    /**
     *
     * @param int $projeto_id
     * @param int $categoria_id
     * @param int $ano
     * @return float 
     */
    public function getTotalBudgetPorCategoria( $projeto_id, $categoria_id, $ano = null )
    {
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	
	$select = $dbBudgetMes->select()
			      ->setIntegrityCheck( false )
			      ->from( 
				  array( 'bp' => $dbBudgetPrevisao ),
				  array( 'total' => new Zend_Db_Expr( 'SUM( bp.fn_budget_previsao_valor )' ) )
			      )
			      ->join(
				  array( 'bm' => $dbBudgetMes ),
				  'bm.fn_budget_mes_id = bp.fn_budget_mes_id',
				  array()
			      )
			      ->join(
				  array( 'tl' => $dbTipoLancamento ),
				  'tl.fn_tipo_lanc_id = bp.fn_tipo_lanc_id',
				  array()
			      )
			      ->join(
				  array( 'b' => $dbBudget ),
				  'bm.fn_budget_id = b.fn_budget_id',
				  array()
			      )
			      ->where( 'b.projeto_id = ?', $projeto_id )
			      ->where( 'tl.fn_categoria_id = ?', $categoria_id );
	
	if ( !empty( $ano ) )
	    $select->where( 'YEAR( bm.fn_budget_mes_data ) = ?', $ano );
		
	$row = $dbBudget->fetchRow( $select );
	
	return $row->total;
    }
    
    /**
     *
     * @return array
     */
    public function replicaValores()
    {
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	
	$dbBudget->getAdapter()->beginTransaction();
	try {
	    
	    $dataForm = $this->_data;
	    
	    $dataIni = new Zend_Date( $dataForm['projeto_inicio'] );
	    $dataFim = new Zend_Date( $dataForm['projeto_final'] );
	    
	    // Coloca datas com o dia 1
	    $dataIni->setDay( 1 );
	    $dataFim->setDay( 1 );
	    	    
	    // Calcula diferenca entre datas
	    $totalMeses = floor( ( $dataFim->getTimestamp() - $dataIni->getTimestamp() ) / ( 60 * 60 * 24 * 30 ) ) + 1;
	    	    
	    // Calcula a divisao do valor pelos meses
	     $dataForm['valor_tipo_lanc'] = round( (float)$dataForm['valor'] / $totalMeses, 2 );
	    	    
	    // Salva budget caso nao tenha ainda
	    $dataForm['fn_budget_id'] = $this->_saveBudget( $dataForm );
	    
	    // Percorre mes a mes
	    for ( $mes = 0; $mes < $totalMeses; $mes++ ) {
		
		// Pega mes e ano
		$dataForm['mes'] = $dataIni->get( Zend_Date::MONTH );
		$dataForm['budget_ano'] = $dataIni->get( Zend_Date::YEAR );
		
		// Salva budget do mes
		$dataForm['fn_budget_mes_id'] = $this->_saveBudgetMes( $dataForm );
		
		// Salva previsao para o mes
		$dataForm['fn_budget_previsao_id'] = $this->_saveBudgetPrevisao( $dataForm );
		
		// Soma total do Mes/Ano para o Budget
		$totalBudgetMes = (float)$this->somaTotalBudgetMes( $dataForm['fn_budget_id'], $dataForm['fn_budget_mes_id'] );

		$whereBudgetMes = $dbBudget->getAdapter()->quoteInto( 'fn_budget_mes_id = ?', $dataForm['fn_budget_mes_id'] );
		$dbBudgetMes->update( array( 'fn_budget_mes_total' => $totalBudgetMes ), $whereBudgetMes );
	
		// Vai para o proximo mes
		$dataIni->addMonth( 1 );
	    }
	    
	    // Pega total do Budget
	    $totalBudget = (float)$this->somaTotalBudget( $dataForm['fn_budget_id'] );
	    
	    // Atualiza total do Budget na base
	    $whereBudget = $dbBudget->getAdapter()->quoteInto( 'fn_budget_id = ?', $dataForm['fn_budget_id'] );
	    $dbBudget->update( array( 'fn_budget_total' => $totalBudget ), $whereBudget );
	    
	    // Calcula o total da categoria para atualizar
	    $totalCategoria = (float)$this->getTotalBudgetPorCategoria( $dataForm['projeto_id'], $dataForm['fn_categoria_id'], $dataForm['budget_ano'] );
	    
	    $dbBudget->getAdapter()->commit();
	    
	    return array(
		'status'		    => true,
		'fn_budget_id'		    => $dataForm['fn_budget_id'],
		'fn_budget_total_categoria' => $totalCategoria,
		'fn_budget_total'	    => $totalBudget
	    );
	    
	} catch ( Exception $e ) {
	    
	    $dbBudget->getAdapter()->rollBack();
	    
	    return array( 'status' => false );
	}
    }
    
    /**
     *
     * @param array $filters
     * @return Zend_Db_Table_Row
     */
    public function valorGeralTipoLancamento( $filters )
    {
	$dbBudgetPrevisao = App_Model_DbTable_Factory::get( 'BudgetPrevisao' );
	$dbBudgetMes = App_Model_DbTable_Factory::get( 'BudgetMes' );
	$dbBudget = App_Model_DbTable_Factory::get( 'Budget' );
	
	$select = $dbBudgetPrevisao->select()
				   ->setIntegrityCheck( false )
				   ->from(
					array( 'bp' => $dbBudgetPrevisao ),
					array( 'total' => new Zend_Db_Expr( 'SUM(fn_budget_previsao_valor)' ) )
				   )
				   ->join(
				       array( 'bm' => $dbBudgetMes ),
				       'bm.fn_budget_mes_id = bp.fn_budget_mes_id',
				       array()
				   )
				   ->join(
				       array( 'b' => $dbBudget ),
				       'b.fn_budget_id = bm.fn_budget_id',
				       array()
				   );
	
	// Filtra por projeto
	if ( !empty( $filters['projeto_id'] ) )
	    $select->where( 'b.projeto_id = ?', (int)$filters['projeto_id'] );
	
	// Filtra pro tipo de lancamento
	if ( !empty( $filters['fn_tipo_lanc_id'] ) )
	    $select->where( 'bp.fn_tipo_lanc_id = ?', (int)$filters['fn_tipo_lanc_id'] );
	
	// Filtra por ano
	if ( !empty( $filters['budget_ano'] ) )
	    $select->where( 'YEAR(bm.fn_budget_mes_data) = ?', (int)$filters['budget_ano'] );
		
	return $dbBudgetPrevisao->fetchRow( $select );
    }
}