<?php

/**
 * 
 * @version $Id: Categoria.php 578 2012-05-11 03:02:23Z helion $
 */
class Model_Mapper_Categoria extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return type 
     */
    public function fetchGrid()
    {
        $dbCategoria = App_Model_DbTable_Factory::get('Categoria');
        $dbProjeto   = App_Model_DbTable_Factory::get('Projeto');

	$select = $dbCategoria->select()
		->setIntegrityCheck( false )
		->from(
			array('c' => $dbCategoria), array('c.*')
		)
		->joinLeft(
		array('c2' => $dbCategoria), 'c.fn_categoria_pai = c2.fn_categoria_id', 
                        array( 'fn_categoria_pai_descricao' => 'c2.fn_categoria_descricao')
                )
		->join(
		array('p' => $dbProjeto), 'c.projeto_id = p.projeto_id', 
                        array( 'projeto_nome' )
	);

	$rows = $dbCategoria->fetchAll( $select );

        $data = array( 'rows' => array() );

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->fn_categoria_id,
                    'data'  => array(
                        ++$key,
                        $row->fn_categoria_descricao,
                        $row->projeto_nome,
                        parent::_showStatus($row->fn_categoria_status)
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
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Categoria' );
	    
	    $where = array( 
		'UPPER(fn_categoria_descricao) = UPPER(?)' => $this->_data['fn_categoria_descricao'],
		'projeto_id = ?' => $this->_data['projeto_id']
	    );
		
	    if ( !$dbTable->isUnique( $where, $this->_data['fn_categoria_id'] ) ) {

		$this->_message->addMessage( 'Categoria já cadastrada com esse nome.', App_Message::ERROR );
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
        $where = array( 'fn_categoria_id = ?' => $this->_data['id'] );

        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Categoria' ), $where );
    }
    
    /**
     *
     * @return type 
     */
    public function listCategoriaTree( $projeto_id )
    {
	$dbCategoria = App_Model_DbTable_Factory::get( 'Categoria' );
	
	$data = $dbCategoria->fetchAll( 
				array( 'projeto_id = ?' => $projeto_id ), 
				array( 'fn_categoria_pai', 'fn_categoria_ordem', 'fn_categoria_descricao' ) 
			    )
			    ->toArray();
	
	return $this->_addChildrenTree( $this->_createRecursiveItens( $data ), 'root' );
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
	    
	    $newPath[] = $row['fn_categoria_descricao'];
	    
	    $item = array(
		'id'	     => $row['fn_categoria_id'],
		'name'	     => $row['fn_categoria_descricao'],
		'agrupador'  => $row['fn_categoria_agrupador'],
		'type'	     => $type,
		'path'	     => implode( ' > ', $newPath )
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
    protected function _createRecursiveItens( $data )
    {
	$dataFinal = new ArrayObject( array() );
        
        $categoria = array();
        foreach ( $data as $row )
            $categoria[$row['fn_categoria_id']] = $row;
	
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
        if ( empty( $row['fn_categoria_pai'] ) ) {
            
            if ( !array_key_exists( $row['fn_categoria_id'], $collection ) ) {
                
                $collection[$row['fn_categoria_id']] = $row;
                
                if ( !array_key_exists( 'children', $collection[$row['fn_categoria_id']] ) )
                    $collection[$row['fn_categoria_id']]['children'] = new ArrayObject( array() );
            }
        } else if ( !$this->_searchParent( $row, $collection ) ) {
	    
            $parent = $source[$row['fn_categoria_pai']];
            $parent['children'] = new ArrayObject( array() );
            $parent['children'][$row['fn_categoria_id']] = new ArrayObject( $row );
	    
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
            if ( $parent['fn_categoria_id'] == $row['fn_categoria_pai'] ) {
                if ( !array_key_exists( 'children', $parent ) )
                    $parent['children'] = new ArrayObject( array() );
                
                if ( !array_key_exists( $row['fn_categoria_id'], $parent['children'] ) )
                        $parent['children'][$row['fn_categoria_id']] = new ArrayObject( $row );
                
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
    public function organizarCategoria( $data )
    {
	$dbCategoria = App_Model_DbTable_Factory::get( 'Categoria' );
	
	$dbAdapter = $dbCategoria->getAdapter();
	
	try {
	    
	    $dbAdapter->beginTransaction();
	    
	    if ( !empty( $data['pai'] ) ) {
		
		$where = $dbAdapter->quoteInto( 'fn_categoria_pai = ?', $data['pai'] );
		$dbCategoria->update( array( 'fn_categoria_pai' => null ), $where );
		
	    }
	    
	    if ( !empty( $data['filhos'] ) ) {
		
		foreach ( $data['filhos'] as $key => $value ) {

		    $where = $dbAdapter->quoteInto( 'fn_categoria_id = ?', $value );
		    $dbCategoria->update( array( 
					'fn_categoria_pai'   => empty( $data['pai'] ) ? null : $data['pai'], 
					'fn_categoria_ordem' => ++$key
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
}