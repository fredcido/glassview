<?php

/**
 * 
 * @version $Id: Menu.php 501 2012-04-15 04:38:17Z fred $
 */
class Model_Mapper_Menu extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return array 
     */
    public function fetchGrid()
    {
        
        $dbMenu = App_Model_DbTable_Factory::get('Menu');
        $dbTela = App_Model_DbTable_Factory::get('Tela');
        
        $select = $dbMenu->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('m' => $dbMenu),
                        array('m.*')
                )
                ->joinLeft(
                        array('t' => $dbTela),
                        'm.tela_id = t.tela_id',
                        array('t.tela_nome')
                )->joinLeft(
                        array('m2' => $dbMenu),
                        'm2.menu_id = m.menu_pai',
                        array('menu_pai_label' => 'm2.menu_label')
                )
		->order( array( 'menu_pai', 'menu_label' ) );

        $rows = $dbMenu->fetchAll( $select );
        
        $data = array('rows' => array());
        
        if ( $rows->count() ) {
            
            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->menu_id,
                    'data'  => array(
                        ++$key,
                        $row->menu_label,
                        $row->menu_pai_label,
                        $row->tela_nome,
                        $this->_getTipoNome( $row->menu_tipo )
                    )
                );
                
            }
            
        }
        
        return $data;
    }
    
    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getTipoNome( $type )
    {
	$optTipo['A'] = 'Aba';
	$optTipo['G'] = 'Agrupador';
	$optTipo['C'] = 'Customizado';
	$optTipo['D'] = 'Dialog';
	
	return empty( $optTipo[ $type ] ) ? 'Desconhecido' : $optTipo[ $type ];
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getMenusTela()
    {
	$menuDb = App_Model_DbTable_Factory::get( 'Menu' );
	$telaDb = App_Model_DbTable_Factory::get( 'Tela' );
	
	$select = $menuDb->select()
			 ->setIntegrityCheck( false )
			 ->from( array( 'm' => $menuDb ) )
			 ->joinLeft(
			    array( 't' => $telaDb ),
			    't.tela_id = m.tela_id
			    AND t.tela_status = 1',
			    array( 'tela_nome', 'tela_path' )
			 )
			 ->order( array( 'm.menu_pai', 'm.menu_ordem', 'm.menu_label' ) );
		
        $data = $menuDb->fetchAll( $select );
        
        return $this->_createRecursiveItens( $data->toArray() ); 
    }
    
    /**
     *
     * @param array $data
     * @return ArrayObject 
     */
    protected function _createRecursiveItens( $data )
    {
	$dataFinal = new ArrayObject( array() );
        
        $menu = array();
        foreach ( $data as $row )
            $menu[$row['menu_id']] = $row;
	
        foreach ( $menu as $row )
            $this->_addItem( $row, $dataFinal, $menu );
	
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
        if ( empty( $row['menu_pai'] ) ) {
            
            if ( !array_key_exists( $row['menu_pai'], $collection ) ) {
                
                $collection[$row['menu_id']] = $row;
                
                if ( !array_key_exists( 'children', $collection[$row['menu_id']] ) )
                    $collection[$row['menu_id']]['children'] = new ArrayObject( array() );
            }
        } else if ( !$this->_searchParent( $row, $collection ) ) {
            
            $parent = $source[$row['menu_pai']];
            $parent['children'] = new ArrayObject( array() );
            $parent['children'][$row['menu_id']] = new ArrayObject( $row );
	    
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
            if ( $parent['menu_id'] == $row['menu_pai'] ) {
                if ( !array_key_exists( 'children', $parent ) )
                    $parent['children'] = new ArrayObject( array() );
                
                if ( !array_key_exists( $row['menu_id'], $parent['children'] ) )
                        $parent['children'][$row['menu_id']] = new ArrayObject( $row );
                
                return true;
                
            } else if ( array_key_exists( 'children', $parent ) && $this->_searchParent( $row, $parent['children'] ) )
                return true;
        }
        
        return false;
    }
    
    /**
     *
     * @return boolean 
     */
    public function save()
    {
        try {
	    
	    return parent::_simpleSave( App_Model_DbTable_Factory::get( 'Menu' ) );
            
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
        $where = array( 'menu_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Menu' ), $where );
    }
    
    /**
     *
     * @return array
     */
    public function getIconsMenu()
    {
	$icones = array( array( 'id' => '', 'name' => '', 'label' => '' ) );
	
	$contents = file_get_contents( APPLICATION_PATH . '/../public/styles/icones.css' );
	
	if ( preg_match_all( '/\.([-\w]+),/i', $contents, $match ) ) {
	    
	    foreach ( $match[1] as $icon ) {
		
		$label = strlen( $icon ) > 23 ? substr( $icon, 0, 23 ) . '...' : $icon;
		
		$icones[] = array( 
		    'id' => $icon,
		    'label' => '<div class="label-menu-item"><span class="' . $icon . '"></span>' . $label . '</div>',
		    'name'  => $icon
		);
	    }
	}
	
	return $icones;
    }
    
    /**
     *
     * @return type 
     */
    public function listMenuTree()
    {
	return $this->_addChildrenTree( $this->getMenusTela(), 'root' );
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    protected function _addChildrenTree( $data, $type )
    {
	$retorno = array();
	
	foreach ( $data as $row ) {
	    
	    $item = array(
		'id'    => $row['menu_id'],
		'name'  => $row['menu_label'],
		'icone' => $row['menu_icon'],
		'type'	=> $type
	    );
	    
	    if ( !empty( $row['children'] ) && $row['children']->count() > 0 )
		$item['children'] = $this->_addChildrenTree( $row['children'] , 'child');
	    
	    $retorno[] = $item;
	}
	
	return $retorno;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function organizarMenu( $data )
    {
	$dbMenu = App_Model_DbTable_Factory::get( 'Menu' );
	
	$dbAdapter = $dbMenu->getAdapter();
	
	try {
	    
	    $dbAdapter->beginTransaction();
	    
	    if ( !empty( $data['pai'] ) ) {
		
		$where = $dbAdapter->quoteInto( 'menu_pai = ?', $data['pai'] );
		$dbMenu->update( array( 'menu_pai' => null ), $where );
		
	    }
	    
	    if ( !empty( $data['filhos'] ) ) {
		
		foreach ( $data['filhos'] as $key => $value ) {

		    $where = $dbAdapter->quoteInto( 'menu_id = ?', $value );
		    $dbMenu->update( array( 
					'menu_pai'   => empty( $data['pai'] ) ? null : $data['pai'], 
					'menu_ordem' => ++$key
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