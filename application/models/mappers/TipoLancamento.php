<?php

/**
 * 
 * @version $Id: TipoLancamento.php 1019 2013-10-10 13:16:50Z helion $
 */
class Model_Mapper_TipoLancamento extends App_Model_Mapper_Abstract
{
    
    /**
     *
     * @return array 
     */
    public function fetchGrid()
    {
	$rows = $this->listAll();
	
        $data = array( 'rows' => array() );

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->fn_tipo_lanc_id,
                    'data'  => array(
                        ++$key,
                        $row->fn_tipo_lanc_cod,
                        $row->fn_tipo_lanc_desc,
                        $row->projeto_nome,
                        $row->fn_categoria_descricao,
                        parent::_showStatus($row->fn_tipo_lanc_status)
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	    
	    $where1 = array( 'UPPER(projeto_id) = UPPER(?)'        => $this->_data['projeto_id'] ,
	                     'UPPER(fn_tipo_lanc_cod)  = UPPER(?)' => $this->_data['fn_tipo_lanc_cod']  );
		
	    if ( !$dbTable->isUnique( $where1, $this->_data['fn_tipo_lanc_id'] ) ) {

		$this->_message->addMessage( 'Código de tipo de lançamento já cadastrado para este projeto.', App_Message::ERROR );
		return false;
	    }
            
	    $where = array( 'UPPER(fn_tipo_lanc_desc) = UPPER(?)' => $this->_data['fn_tipo_lanc_desc'] ,
	                    'UPPER(fn_tipo_lanc_cod)  = UPPER(?)' => $this->_data['fn_tipo_lanc_cod']  );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['fn_tipo_lanc_id'] ) ) {

		$this->_message->addMessage( 'Tipo de Lançamento já cadastrado.', App_Message::ERROR );
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
        $where = array( 'fn_tipo_lanc_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'TipoLancamento' ), $where );
    }
    
    /**
     *
     * @return type 
     */
    public function listTipoLancamentoTree( $id )
    {
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	
	$data = $dbTipoLancamento->fetchAll( 
				    array( 'fn_categoria_id = ?' => $id ), 
				    array( 'fn_tipo_lanc_pai', 'fn_tipo_lanc_ordem', 'fn_tipo_lanc_desc' ) 
				 )
				 ->toArray();
	
	return $this->_addChildrenTree( $this->createRecursiveItens( $data ), 'root' );
    }
    
    /**
     *
     * @return type 
     */
    public function listTipoLancamentoTreeProjeto( $id )
    {
        $dbCategoria      = App_Model_DbTable_Factory::get( 'Categoria' );
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
        
	$selectCategoria = $dbCategoria->select()
                                        ->from( 
                                               array( 'c' => $dbCategoria ),
                                               array( 
                                                      'fn_tipo_lanc_id'        => 'CONCAT("CAT_",fn_categoria_id)',
                                                      'fn_tipo_lanc_pai'       => 'CONCAT("CAT_",fn_categoria_pai)',
                                                      'fn_tipo_lanc_cod'       => 'CONCAT("")',
                                                      'fn_tipo_lanc_desc'      => 'fn_categoria_descricao',
                                                      'fn_tipo_lanc_agrupador' => 'CONCAT("1")'
                                                   )
                                              )
                                        ->where( 'fn_categoria_status = ?', 1)
                                        ->where( 'projeto_id = ?', (int)$id); 
        
	$selectTipoLancamento = $dbTipoLancamento->select()
				   ->from( 
                                          array( 't' => $dbTipoLancamento ),
                                          array( 
                                                 'fn_tipo_lanc_id',
                                                 'fn_tipo_lanc_pai' => 'IFNULL(fn_tipo_lanc_pai,CONCAT("CAT_",fn_categoria_id))',
                                                 'fn_tipo_lanc_cod',
                                                 'fn_tipo_lanc_desc',
                                                 'fn_tipo_lanc_agrupador'
                                              )
                                         )
                                   ->where( 'fn_tipo_lanc_status = ?', 1)
                                   ->where( 'projeto_id = ?', (int)$id); 
        
        $select = $dbTipoLancamento->select()
            ->union(array($selectCategoria, $selectTipoLancamento))
            ->order("fn_tipo_lanc_pai");

	$data = $dbTipoLancamento->fetchAll( $select )->toArray();
        
	
	return $this->_addChildrenTree( $this->createRecursiveItens( $data ), 'root' );
    }
    
    /**
     *
     * @return type 
     */
    public function buscaPaiTipoLancamentoTreeProjeto( $id )
    {
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	
	$data = $dbTipoLancamento->fetchRow( 
				    array( 'fn_tipo_lanc_id = ?' => $id ), 
				    array( 'fn_tipo_lanc_pai', 'fn_tipo_lanc_ordem', 'fn_tipo_lanc_desc' ) 
				 )
				 ->toArray();

        return $data;
    }
    
    /**
     *
     * @return type 
     */
    public function montaPathTipoLancamentoTreeProjeto( $id )
    {
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	
	$data = $dbTipoLancamento->fetchRow( 
				    array( 'fn_tipo_lanc_id = ?' => (int) $id ), 
				    array( 'fn_tipo_lanc_pai','fn_tipo_lanc_pai', 'fn_tipo_lanc_ordem', 'fn_tipo_lanc_desc' ) 
				 );
        
        if(empty($data))
                return '-';
        
        $aPath[] = $data->fn_tipo_lanc_cod.' '.$data->fn_tipo_lanc_desc;
        while (!empty($data->fn_tipo_lanc_pai)) {

            $data = $this->buscaPaiTipoLancamentoTreeProjeto($data->fn_tipo_lanc_pai);
            $aPath[] = $data->fn_tipo_lanc_cod.' '.$data->fn_tipo_lanc_desc;
        }
        
        $total = count($aPath)-1;
        while (!empty($aPath[$total])) {
            
            if(empty($sPath)){
                
                $sPath = ' '.$aPath[$total];
            }else {
                
                $sPath .= ' > '.$aPath[$total];
            }
            $total--;
        }
        
        unset($aPath);
        return $sPath;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    protected function _addChildrenTree( $data, $type, $path = array() )
    {
	$retorno = array();
	
	foreach ( $data as $row ) {
	    
	    $newPath = $path;
	    
	    $name = $row['fn_tipo_lanc_cod'] . ' ' . $row['fn_tipo_lanc_desc'];
	    
	    $newPath[] = $name;
	    
	    $item = array(
		'id'    => $row['fn_tipo_lanc_id'],
		'name'  => $name,
		'type'	=> $type,
		'path'	=> implode( ' > ', $newPath )
	    );
	    
	    if ( !empty( $row['children'] ) && $row['children']->count() > 0 )
		$item['children'] = $this->_addChildrenTree( $row['children'] , 'child', $newPath );
	    
	    $retorno[] = $item;
	}
	
	return $retorno;
    }
    
    /**
     *
     * @param array $data
     * @return ArrayObject 
     */
    public function createRecursiveItens( $data )
    {
	$dataFinal = new ArrayObject( array() );
        
        $categoria = array();
        foreach ( $data as $row )
            $categoria[$row['fn_tipo_lanc_id']] = $row;
	
        foreach ( $categoria as $row )
            $this->_addItem( $row, $dataFinal, $categoria );
	
        return $dataFinal;
    }
    
        /**
     *
     * @param array $row
     * @param ArrayObject $collection
     * @param array $source 
     */
    protected function _addItem( $row, $collection, $source )
    {
        if ( empty( $row['fn_tipo_lanc_pai'] ) ) {
            
            if ( !array_key_exists( $row['fn_tipo_lanc_id'], $collection ) ) {
                
                $collection[$row['fn_tipo_lanc_id']] = $row;
                
                if ( !array_key_exists( 'children', $collection[$row['fn_tipo_lanc_id']] ) )
                    $collection[$row['fn_tipo_lanc_id']]['children'] = new ArrayObject( array() );
            }
        } else if ( !$this->_searchParent( $row, $collection ) ) {
	    
            $parent = $source[$row['fn_tipo_lanc_pai']];
            $parent['children'] = new ArrayObject( array() );
            $parent['children'][$row['fn_tipo_lanc_id']] = new ArrayObject( $row );
	    
            $this->_addItem( $parent, $collection, $source );
        }
    }
    
    /**
     *
     * @param array $row
     * @param ArrayObject $collection
     * @return bool
     */
    protected function _searchParent( $row, $collection )
    {
        foreach ( $collection as $parent ) {
            if ( $parent['fn_tipo_lanc_id'] == $row['fn_tipo_lanc_pai'] ) {
                if ( !array_key_exists( 'children', $parent ) )
                    $parent['children'] = new ArrayObject( array() );
                
                if ( !array_key_exists( $row['fn_tipo_lanc_id'], $parent['children'] ) )
                        $parent['children'][$row['fn_tipo_lanc_id']] = new ArrayObject( $row );
                
                return true;
                
            } else if ( array_key_exists( 'children', $parent ) && $this->_searchParent( $row, $parent['children'] ) )
                return true;
        }
        
        return false;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function organizarTipoLancamento( $data )
    {
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	
	$dbAdapter = $dbTipoLancamento->getAdapter();
	
	try {
	    
	    $dbAdapter->beginTransaction();
	    
	    if ( !empty( $data['pai'] ) ) {
		
		$where = $dbAdapter->quoteInto( 'fn_tipo_lanc_pai = ?', $data['pai'] );
		$dbTipoLancamento->update( array( 'fn_tipo_lanc_pai' => null ), $where );
		
	    }
	    
	    if ( !empty( $data['filhos'] ) ) {
		
		foreach ( $data['filhos'] as $key => $value ) {

		    $where = $dbAdapter->quoteInto( 'fn_tipo_lanc_id = ?', $value );
		    $dbTipoLancamento->update( array( 
					'fn_tipo_lanc_pai'   => empty( $data['pai'] ) ? null : $data['pai'], 
					'fn_tipo_lanc_ordem' => ++$key
				     ), $where );
		}
	    }
	    
	    $dbAdapter->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbAdapter->rollBack();
	    
	    return array( 'status' => false, 'message' => $this->_config->messages->error );
	}
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listAll()
    {
	$dbCategoria = App_Model_DbTable_Factory::get( 'Categoria' );
	$dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
	$dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
	
	$select = $dbTipoLancamento->select()
				   ->setIntegrityCheck( false )
				   ->from( array( 'tl' => $dbTipoLancamento ) )
				   ->join(
					array( 'c' => $dbCategoria ),
					'c.fn_categoria_id = tl.fn_categoria_id',
					array( 'fn_categoria_descricao' )
				   )
				   ->join(
					array( 'p' => $dbProjeto ),
					'p.projeto_id = tl.projeto_id',
					array( 'projeto_nome' )
				   )
				   ->order( 'tl.fn_tipo_lanc_desc' );
	
	return $dbTipoLancamento->fetchAll( $select );
    }
    
    public function fetchTipoLancamentoPorProjeto()
    {
        $dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
        
        $select = $dbTipoLancamento->select()
            ->from( 
                array( 'tl' => $dbTipoLancamento ),
                array( 'tl.*' )
            )
            ->where('tl.fn_tipo_lanc_status = ?', 1)
            //->where('tl.fn_tipo_lanc_agrupador = ?', 0)
            ->where('tl.projeto_id = ?', $this->_data['projeto_id']);

        $rows = $dbTipoLancamento->fetchAll( $select );
        
        return $rows;
        
/*
        $data = array();

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->fn_tipo_lanc_id,
                    'name'  => $row->fn_tipo_lanc_desc
                );

            }

        }

        return $data;
*/
    }
    
    public function buscaTipoLancamentoCodigo()
    {
        $dbTipoLancamento = App_Model_DbTable_Factory::get( 'TipoLancamento' );
        
        $select = $dbTipoLancamento->select()
            ->from( 
                array( 'tl' => $dbTipoLancamento ),
                array( 'tl.*' )
            )
            ->where('tl.fn_tipo_lanc_cod = ?', (int)trim($this->_data['fn_tipo_lanc_cod']))
            ->where('tl.projeto_id = ?',       (int)trim($this->_data['projeto_id']));

        $rows = $dbTipoLancamento->fetchRow( $select );
        
        
        if(!empty($rows)){
            
            $data = $rows->toArray();

            $data['path'] = $this->montaPathTipoLancamentoTreeProjeto($data['fn_tipo_lanc_id']);
        }
        return $data;
    }
}
