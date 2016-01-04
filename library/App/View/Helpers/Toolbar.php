<?php

/**
 * 
 */
class App_View_Helper_Toolbar extends Zend_View_Helper_Abstract
{
    /**
     *
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;
    
    /**
     *
     * @var string
     */
    protected $_prefixId = 'button_';
    
    /**
     *
     * @var array
     */
    protected $_customButton = array();
    
    /**
     *
     * @var boolean
     */
    protected $_renderDefaultButtons = true;
    
    /**
     *
     * @var array
     */
    protected $_defaultButtons = array( 'salvar', 'editar', 'atualizar' );
    
    /**
     * 
     */
    public function __construct()
    {
	$this->_request = Zend_Controller_Front::getInstance()->getRequest();
    }


    /**
     *
     * @return Zend_Form_DisplayGroup 
     */
    public function toolbar()
    {
        return $this;
    }
    
    /**
     *
     * @param boolean $flag
     * @return \App_View_Helper_Toolbar 
     */
    public function setRenderDefaultButtons( $flag )
    {
	$this->_renderDefaultButtons =  (boolean)$flag;
	return $this;
    }
    
    /**
     *
     * @param array $buttons
     * @return \App_View_Helper_Toolbar
     * @throws Exception 
     */
    public function setDefaultButtons( array $buttons )
    {
	$buttons = array_intersect( $buttons, $this->_defaultButtons );
	
	if ( empty( $buttons ) )
	    throw new Exception( 'Botoes não aceitos para a toolbar' );
	
	$this->_defaultButtons = $buttons;
	
	return $this;
    }
    
    /**
     *
     * @param array $button 
     */
    public function addCustomButton( array $button )
    {
	$this->_customButton[] = $button;
	
	return $this;
    }
    
    /**
     *
     * @return Zend_Form_DisplayGroup 
     */
    public function __toString() 
    {   
	$defaultButtons = array();
	
	if ( $this->_renderDefaultButtons ) {
	    
	    if ( in_array( 'salvar', $this->_defaultButtons ) )
		    $defaultButtons[] = $this->_getNovoButton();
	    
	    if ( in_array( 'editar', $this->_defaultButtons ) )
		    $defaultButtons[] = $this->_getEditarButton();
	    
	    if ( in_array( 'atualizar', $this->_defaultButtons ) )
		    $defaultButtons[] = $this->_getAtualizarButton();
	}
	
	$defaultButtons = array_merge( $defaultButtons, $this->_customButton );
        
	$display = App_Forms_Toolbar::build( $defaultButtons );
	
        return empty( $display ) ? '' : $display->render();
    }
    
    /**
     *
     * @return array
     */
    protected function _getNovoButton()
    {
        $identifier = $this->_getIdentifier( 'Salvar' );
        
        $salvar = array(
            'action' => $identifier,
            'id'     => $this->_prefixId . $identifier . '-salvar',
            'label'  => 'Novo',
            'icon'   => 'dijitIconNewTask',
            'click'  => $this->_getObjJavascript() . '.novo();'
        );
        
        return $salvar;
    }
    
    /**
     *
     * @return array
     */
    protected function _getEditarButton()
    {
        $identifier = $this->_getIdentifier( 'Salvar' );
        
        $editar = array(
            'action' => $identifier,
            'id'     => $this->_prefixId . $identifier . '-alterar',
            'label'  => 'Editar',
            'icon'   => 'dijitIconEdit',
            'click'  => $this->_getObjJavascript() . '.edit();'
        );
        
        return $editar;
    }
    
    /**
     *
     * @return array
     */
    protected function _getAtualizarButton()
    {
        $atualizar = array(
            'id'     => $this->_prefixId . $this->_getIdentifier( 'Atualizar' ),
            'label'  => 'Atualizar',
            'icon'   => 'dijitIconUndo',
            'click'  => $this->_getObjJavascript() . '.atualizarGrid();'
        );
        
        return $atualizar;
    }
    
    /**
     *
     * @param string $action
     * @return string 
     */
    protected function _getIdentifier( $action )
    {
        return App_Plugins_Acl::getIdentifier( $this->_getPath() , $action );
    }
    
    /**
     *
     * @return string
     */
    protected function _getPath()
    {
        return '/' . $this->_request->getModuleName() . '/' . $this->_request->getControllerName() . '/';
    }
    
    /**
     *
     * @return string 
     */
    protected function _getObjJavascript()
    {
        return lcfirst( $this->_toCamelCase( $this->_request->getModuleName() ) ) . 
	       $this->_toCamelCase( $this->_request->getControllerName() );
    }
    
    /**
     *
     * @param string $string
     * @return string 
     */
    protected function _toCamelCase( $string )
    {
	$pieces = explode( '-', $string );

	return implode( '', array_map( 'ucfirst', $pieces ) );
    }
}