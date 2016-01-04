<?php

/**
 * 
 * @version $Id $
 */
class Zend_View_Helper_TreeTipoLancamento extends Zend_View_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_data = array(
                            'identifier'    => 'id',
                            'label'         => 'label',
                            'items'         => array() 
                    );
    
    /**
     * 
     * @access public
     * @param Zend_Db_Table_Rowset $rows
     * @return array 
     */
    public function treeTipoLancamento ( $rows )
    {
        $data = array();
        
        foreach ( $rows as $row )
            $this->_setChildren( $row, $data );

        $this->_setData( $data );
        
        return $this->_data;
    }
    
    /**
     * 
     * @param App_Model_DbTable_Row_Abstract $row
     * @param array $data
     * @param string $separator
     */
    protected function _setChildren ( $row, &$data, $separator = '&nbsp;&nbsp;' )
    {
        if(empty($row->fn_tipo_lanc_cod)){
            
            $label = $row->fn_tipo_lanc_desc;
        }else{
            
            $label = '&nbsp;&nbsp;'.$row->fn_tipo_lanc_cod . ' ' . $row->fn_tipo_lanc_desc;
        }
        
	$name = $label;
	
	if ( !empty( $row->fn_tipo_lanc_agrupador ) ) {
	    
	    //$label = sprintf( '<i><font color="#9EA1A3">%s</font></i>', $label );
	    $name = '';
	}
	
        if ( empty($row->fn_tipo_lanc_pai) ) {
            
            if ( empty($data[$row->fn_tipo_lanc_id]) ) {
            
                $data[$row->fn_tipo_lanc_id] = array(
                    $row->fn_tipo_lanc_id   => array(
			'id'	=>  $row->fn_tipo_lanc_id,
			'label' =>  $label,
			'name'	=>  $name
		    ),
                    'children'              => array()
                );
            
            }
            
        } else {
            
            foreach ( $data as $key => $value ) {
                
                if ( $key == $row->fn_tipo_lanc_pai ) {
                                    
                    if ( empty($data[$key]['children'][$row->fn_tipo_lanc_id]) ) {
			
			$label = $row->fn_tipo_lanc_cod . ' ' . $row->fn_tipo_lanc_desc;
			$name = $label;

			if ( !empty( $row->fn_tipo_lanc_agrupador ) ) {

			    $label = sprintf( '<i><font color="#9EA1A3">%s</font></i>', $label );
			    $name = '';
			}
                    
                        $data[$key]['children'][$row->fn_tipo_lanc_id] = array(
                            $row->fn_tipo_lanc_id   => array(
				'id'	    =>  $row->fn_tipo_lanc_id,
				'label'	    =>	$this->_entity($separator) . $label,
				'name'	    =>	$label
			    ),
                            'children'              => array()
                        );
                    
                    }

                } else {
                    
                    $this->_setChildren( $row, $data[$key]['children'], $this->_entity($separator . '&nbsp;&nbsp;') ); 
                    
                }
                
            }
            
        }
    }

    /**
     * @access protected
     * @param string $value
     * @param int $flags
     * @param string $encoding
     * @return string
     */
    protected function _entity( $value, $flags = ENT_COMPAT, $encoding = 'UTF-8' )
    {
        return html_entity_decode( $value, $flags, $encoding );
    }
        
    /**
     * @access protected
     * @param array $param
     * @return array
     */
    protected function _setData( array $param )
    {
        $iterator = new RecursiveArrayIterator( $param );
        
        iterator_apply( $iterator, array($this, '_traverseStructure'), array($iterator) );
    }
    
    /**
     * 
     * @access protected
     * @param RecursiveArrayIterator $iterator
     * @return void
     */
    protected function _traverseStructure ( $iterator )
    {
	$items = array();
	
        while ( $iterator->valid() ) {
	    
            if ( $iterator->hasChildren() ) {
                    
                $this->_traverseStructure( $iterator->getChildren() );
                
            } else {
		
		if ( !in_array( $iterator['id'], $items ) ) {
		       
		    $value = array(
			'id'    => $iterator['id'],
			'name'  => $iterator['name'],
			'label' => $iterator['label']
		    );

		    array_push( $this->_data['items'], $value );

		    $items[] = $iterator['id'];

		}
            }
                
                
            $iterator->next();
        }
    }
        
}